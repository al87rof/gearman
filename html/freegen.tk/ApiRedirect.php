<?php
class Faucet {
    public $page;    // хранит результат запроса КУРЛ в том числе исходный код страницы
    public $page2;    // хранит результат запроса КУРЛ в том числе исходный код страницы
    public $faucetName; //Хранит имя крана (http://example.com)
    public $ipArray;
    public $ipCurrent;
    public $ipGood;    // Массив рабочих Proxy 

    public function getConnection($name) {
        $uagent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:45.0) Gecko/20100101 Firefox/45.0";
        $ch = curl_init($name);
        $path = dirname(__FILE__);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
        curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
        curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
        curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); // таймаут соединения
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);        // таймаут ответа
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);       // останавливаться после 10-ого редиректа
        curl_setopt($ch, CURLOPT_COOKIEFILE, $path . "\my_cookies.txt"); //Создание файла куков
        curl_setopt($ch, CURLOPT_COOKIEJAR, $path . "\my_cookies.txt");
        return $ch;
    }

    public function getPage($name) {
        $ch = $this->getConnection($name);
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
    
    function postApiIpredirect($name,$ip) {
    $ch = $this->getConnection($name);
    curl_setopt($ch, CURLOPT_REFERER, $name);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "ApiIpRedirect=$ip");
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}
    
}
    $name_i = "http://infor.freeoda.com/";
    $name_f = "http://freegen.tk/ApiFaucet2.php";
    
    $FaucetObject2 = new Faucet();
   while (true){
    $FaucetObject2->getPage($name_i);
    $result2 = $FaucetObject2->page;
    //Перенос кода хтмл в переменную
    $html = $result2['content'];
    //Парсинг ИП
    $h = explode("<td width=\"45%\" height=\"25\" bgcolor=\"#F7F7F7\"><b>", $html);
    $h2 = explode("</b>", $h[6]);
    $parseIp = $h2[0];    
    //Обрезка спец символов 
    $parseIp = trim($parseIp);
    //Отправка ИП на сервер
    $FaucetObject2->postApiIpredirect($name_f, $parseIp);
    echo "IP:".$parseIp."\n";
    echo date('H:i:s', time()) . "\n";
    echo "*************************\n";
    sleep(10);
}
//header( 'Refresh: 0; url=/error404.html' );
    
  


