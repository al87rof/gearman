<?php

/**
 * Description of AFaucet
 *
 * @author Sanja
 */
//require_once ROOT.'\classes\Hoard.php';

abstract class AFaucet {

    public $page;    // хранит результат запроса КУРЛ в том числе исходный код страницы
    public $page2;    // хранит результат запроса КУРЛ в том числе исходный код страницы
    public $faucetName; //Хранит имя крана (http://example.com)
    public $ipArray;
    public $ipCurrent;
    public $ipGood;    // Массив рабочих Proxy 

    /*
     * Конструктор принимает название крана
     * Конструктор читает с файла спискок Ип адрессов
     * сохраняет их в переменную $ipArray (массив)
     */

    public function __construct($FaucetName) {

        $this->faucetName = $FaucetName;
        $file = ROOT . '/iplist.txt';

        if (is_file($file)) {
            $f = fopen($file, "r");
            while ($line = fgets($f)) {
                $this->ipArray[] = $line;
            }
        }
    }

    /*
     * Метод getConnection($ip) принимает параметр ip (proxy) 
     * подключяется к сайту через прокси
     * возвращяет дескриптор подключения
     */

    public function getConnection($ip) {

        $uagent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:45.0) Gecko/20100101 Firefox/46.0";
        $name = $this->faucetName;
        $ch = curl_init($name);
        $path = dirname(__FILE__);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
        curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
        curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
        curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
        curl_setopt($ch, CURLOPT_PROXY, "$ip");        // использование прокси
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4); // таймаут соединения
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);        // таймаут ответа
        curl_setopt($ch, CURLOPT_MAXREDIRS, 4);       // останавливаться после 10-ого редиректа
        curl_setopt($ch, CURLOPT_COOKIEFILE, $path . "\my_cookies.txt"); //Создание файла куков
        curl_setopt($ch, CURLOPT_COOKIEJAR, $path . "\my_cookies.txt");

        return $ch;
    }

    /*
     * Метод getPage() принимает параметр $count (счетчик ip адрессов)
     * вытягивает ip из массива ipArray
     * сохраняет исходный код страницы в переменную $Page
     */

    public function getPage($proxyIp) {

        $ch = $this->getConnection($proxyIp);
        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);
        curl_close($ch);

        $header['errno'] = $err;
        $header['errmsg'] = $errmsg;
        $header['content'] = $content;
        $this->page = $header;
        
    }

    /*
     * Метод postPage() принимает параметры:
     * name_text_vallet - имя поля ввода кошелька 
     * vallet_new -номер кошелька
     * count - счетчик кошелька 
     * Подготавливает и отправляет пост запрос на сервер
     */

    abstract public function postPage($walletNameTextField, $wallet, $value1, $key1, $value2, $key2, $proxyIp);

    /*
     * Метод парсит капчу с исходного кода страницы
     * возвращяет капчу в виде хтлм кода
     * или сообщение об ошибке
     */

    abstract public function parseCaptcha();

    /*
     * Метод парсит имя поля кошелька с исходного кода страницы
     * возвращяет имя поля кошелька
     * или сообщение об ошибке
     */

    abstract public function parseNameTextFieldWallet();

    /*
     * Метод проверки капчи
     * возвращяет результат
     */

    abstract public function parseCaptchaValid();

    /*
     * Метод получяет счетчик кошелька с файла wlcount.txt
     */

    public function getWalletCount() {

        $file = ROOT . '\components\wlcount.txt';
        $f = fopen($file, "r");
        flock($f, 2);
        $vlcount = 0;
        while ($line = fread($f, filesize($file))) {
            $vlcount = $line;
        }
        flock($f, 3);
        fclose($f);
        return $vlcount;
    }

    /*
     * Метод записывает счетчик кошелька в файл wlcount.txt
     */

    public function setWalletCount($vlcount) {

        $file = ROOT . '\components\wlcount.txt';
        $f = fopen($file, "w");
        flock($f, 2);
        fwrite($f, $vlcount);
        flock($f, 3);
        fclose($f);
    }

    /*
     * 
     * Метод записывает рабочий IP proxy в файл goodProxy.txt
     */

    public function setGoodProxy($ip) {

        $file = ROOT . '\components\goodProxy.txt';
        $f = fopen($file, "a");
        flock($f, 2);
        fwrite($f, $ip);
        flock($f, 3);
        fclose($f);
    }

    /*
     * 
     * Метод записывает не рабочий IP proxy в файл badProxy.txt
     */

    public function setBadProxy($ip) {

        $file = ROOT . '\components\badProxy.txt';
        $f = fopen($file, "a");
        flock($f, 2);
        fwrite($f, $ip);
        flock($f, 3);
        fclose($f);
    }

    /*
     * 
     * Метод читает файл goodProxy.txt в масив ipGood
     */

    public function getArrayGoodProxy() {

        $file = ROOT . '\components\goodProxy.txt';

        if (is_file($file)) {
            $f = fopen($file, "r");
            while ($line = fgets($f)) {
                $this->ipGood[] = $line;
            }
        }
    }

    /*
     * Метод ищет первый рабочий Proxy
     * Возвращяет его
     * сортирует список согласно алгоритму
     * не рабочие Proxy удаляет 
     */

    public function sortArray() {

        $ipArray = $this->ipGood;
        $ip = '';
        foreach ($ipArray as $key => $value) {

            $faucetOb = new Hoard("http://ok.ru");
            $faucetOb->getPage($value);
            $result = $faucetOb->page;

            if ($result['http_code'] == 200 && $result['total_time'] <= 3) {
                if (stristr($result['content'], "Please complete the security check to access") == false) {
                    $ip = $value;
                    unset($ipArray[$key]);
                    $file = ROOT . '\components\goodProxy.txt';
                    $f = fopen($file, "a+");
                    flock($f, 2);
                    ftruncate($f, 0);
                    flock($f, 3);
                    fclose($f);

                    foreach ($ipArray as $value2) {
                        $file = ROOT . '\components\goodProxy.txt';
                        $f = fopen($file, "a");
                        flock($f, 2);
                        fwrite($f, $value2);
                        flock($f, 3);
                        fclose($f);
                    }
                    $this->setGoodProxy($ip);
                    break;
                } else {
                    unset($ipArray[$key]);
                }
            } else {
                unset($ipArray[$key]);
            }
        }
        return $ip;
    }

    /*
     * Метод получяет счетчик Обьекта  с файла obcount.txt
     */

    public function getObjectCount() {

        $file = ROOT . '\components\obcount.txt';
        $f = fopen($file, "r");
        flock($f, 2);
        $obcount = 0;
        while ($line = fread($f, filesize($file))) {
            $obcount = $line;
        }
        flock($f, 3);
        fclose($f);
        return $obcount;
    }

    /*
     * Метод записывает счетчик кошелька в файл wlcount.txt
     */

    public function setObjectCount($obcount) {

        $file = ROOT . '\components\obcount.txt';
        $f = fopen($file, "w");
        flock($f, 2);
        fwrite($f, $obcount);
        flock($f, 3);
        fclose($f);
    }

}
