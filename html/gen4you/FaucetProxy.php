<?php

/**
 * Description of Faucet
 *
 * @author Admin
 */
class Faucet {

    public $page;    // хранит результат запроса КУРЛ в том числе исходный код страницы
    public $page2;    // хранит результат запроса КУРЛ в том числе исходный код страницы
    public $faucetName; //Хранит имя крана (http://example.com)
    public $ipbase;
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
        $file = 'iplist.txt';

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

        $uagent = "Mozilla/6.0 (Windows NT 7.1; WOW64; rv:49.0) Gecko/20100401 Firefox/49.0";
        $name = $this->faucetName;
        $ch = curl_init($name);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
        curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
        curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
        curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
        curl_setopt($ch, CURLOPT_PROXY, "$ip");        // использование прокси
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6); // таймаут соединения
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);        // таймаут ответа
        curl_setopt($ch, CURLOPT_MAXREDIRS, 6);       // останавливаться после 10-ого редиректа
        curl_setopt($ch, CURLOPT_COOKIEFILE, ROOT . "/cookies/blue.txt"); //Создание файла куков
        curl_setopt($ch, CURLOPT_COOKIEJAR, ROOT . "/cookies/blue.txt");

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
     * 
     * Метод записывает рабочий IP proxy в файл goodProxy.txt
     */

    public function setGoodProxy($ip) {

        $file = ROOT . '/components/goodProxy.txt';
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

        $file = ROOT . '/components/badProxy.txt';
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

        $file = ROOT . '/components/goodProxy.txt';

        if (is_file($file)) {
            $f = fopen($file, "r");
            while ($line = fgets($f)) {
                $this->ipGood[] = $line;
            }
        }
    }

   

    
}
