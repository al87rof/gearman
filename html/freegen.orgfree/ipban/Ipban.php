<?php

class Ipban {
    
    public $name;
    public $page;
            
    function __construct($name) {
        $this->name = $name;        
    }


    public function getConnection() {

        $uagent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:45.0) Gecko/20100101 Firefox/46.0";
        $name = $this->name;
        $ch = curl_init($name);
        $path = dirname(__FILE__);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
        curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
        curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
        curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4); // таймаут соединения
        curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);//ssl
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);        // таймаут ответа
        curl_setopt($ch, CURLOPT_MAXREDIRS, 4);       // останавливаться после 10-ого редиректа
        curl_setopt($ch, CURLOPT_COOKIEFILE, $path . "\my_cookies.txt"); //Создание файла куков
        curl_setopt($ch, CURLOPT_COOKIEJAR, $path . "\my_cookies.txt");

        return $ch;
    }
    
    public function getPage(){
        $ch = $this->getConnection();
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
    
    public function postPageApi($arrayIp) {
    $ch = $this->getConnection();
    $name = $this->name;
    curl_setopt($ch, CURLOPT_REFERER, $name);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "banip=$arrayIp");
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}
    
    public function readFileCSV($filePath) {
        $a = array();
        $f = fopen("$filePath", 'r');

        while ($line = fgets($f)) {
            $a[] = explode(";", $line);
        }
        fclose($f);
        return $a;
    }
    
    /*
     * Витягивает ип адреса с двумерного масива
     * в одномерный и возращяет его
     */  
    public function array2TOarray1(array $arr) {
        $array = array();
        foreach ($arr as $v) {
            $array[] = trim($v[0]);
        }
        return $array;
    }
    
    /*
     *Сохраняет контент в csv файл 
     */
    public function saveContent2CSV($content,$filePath){
        $f = fopen("$filePath", 'w+');
        fwrite($f, $content);
        fclose($f);
    }
       

}
while(true){
$arr = array();     // Хранит двумерный массив 
$array1 = array();  // Хранит одномерный массив
$filePath = "./input_files/blocklist.csv";
$ipBan = new Ipban("https://blocklist.net.ua/blocklist.csv");
$ipBan->getPage();
$content = $ipBan->page['content'];
$ipBan->saveContent2CSV($content,"$filePath");

$arr    = $ipBan->readFileCSV($filePath);
$array1 = $ipBan->array2TOarray1($arr);

//Создаем обьект и начинаем сериализацию
$url = "http://gen4you.ml/api.php";
// старій домен $url = "http://freegen.tk/api.php";
$ipBanFreegen = new Ipban($url); 
$array1 = base64_encode(serialize($array1));
$result = $ipBanFreegen->postPageApi($array1);
echo "$result\n";
sleep(600);
}

