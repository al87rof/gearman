<?php

function getConnection($name) {
    $uagent = "Mozilla/6.0 (Windows NT 7.1; WOW64; rv:49.0) Gecko/20100401 Firefox/49.0";
    $ch = curl_init($name);
    //$path = dirname(__FILE__);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
    curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
    curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
    curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 25); // таймаут соединения
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);        // таймаут ответа
    curl_setopt($ch, CURLOPT_MAXREDIRS, 25); // останавливаться после 10-ого редиректа
    curl_setopt($ch, CURLOPT_COOKIEFILE, "my_cookies.txt"); //Создание файла куков
    curl_setopt($ch, CURLOPT_COOKIEJAR, "my_cookies.txt");
    return $ch;
}

function postPageApiFaucet($arrayIp, $name) {
    $ch = getConnection($name);
    curl_setopt($ch, CURLOPT_REFERER, $name);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "ApiFaucet=$arrayIp");
    $res = curl_exec($ch);
    curl_close($ch);
}

function postPageWorkerClient($name) {
    $ch = getConnection($name);
    curl_setopt($ch, CURLOPT_REFERER, $name);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "");
    $res = curl_exec($ch);
    sleep(30);
    curl_close($ch);
    return $res;
}

function postPageApiBack($name) {
    $ch = getConnection($name);
    curl_setopt($ch, CURLOPT_REFERER, $name);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "ApiBack=true");
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}

function getArrayGoodProxy() {

    $file = './components/goodProxy.txt';
    if (is_file($file)) {
        $f = fopen($file, "r");
        while ($line = fgets($f)) {
            $ipGood[] = $line;
        }
    } else {
        echo "ERROR READ FILE";
    }
    return $ipGood;
}

function setArrayAllProxy($arrayAllProxy) {
    $result = implode("", $arrayAllProxy);
    $file = './components/goodProxy.txt';
    $f = fopen($file, "a+");
    flock($f, 2);
    fwrite($f, $result);
    flock($f, 3);
    fclose($f);
}

function clearFile() {

    $file = './components/goodProxy.txt';
    $f = fopen($file, "w+");
    flock($f, 2);
    fwrite($f, "");
    flock($f, 3);
    fclose($f);
}

function getUniqueIp($ipGood) {
    $arrayUnique = array();
    $ipTmp = array();
    $portTmp = array();
    $arrayUniqueLast = array();
    //Цыкл разделения строки IP \ Port
    foreach ($ipGood as $key => $value) {
        $clear = explode(":", $value);
        $ipTmp[] = $clear[0];
        $portTmp[] = $clear[1];
    }


    //Цыкл удаления повторяющихся Ip
    $c = count($ipTmp);
    for ($i = 0; $i < $c - 1; $i++) {

        for ($j = $i + 1; $j < $c; $j++) {

            if ($ipTmp[$i] == $ipTmp[$j]) {
                unset($ipTmp[$i]);
                break;
            }
        }
    }
    $arrayUnique = $ipTmp;
    //Цыкл склеивания IP+PORT
    foreach ($arrayUnique as $key => $value) {
        $arrayUniqueLast[] = $value . ":" . $portTmp[$key];
    }
    return $arrayUniqueLast;
}

$name = "http://123.1.1.240/ApiFaucet2.php";
$WorkerName = "http://localhost/freegen.orgfree/worker.php";
$clienName = "http://localhost/freegen.orgfree/client.php";
$clienName2 = "http://localhost/freegen.orgfree/client2.php";
while (true) {
//Очистка файла
    
    clearFile();
//Запуск клиента
   postPageWorkerClient($clienName);
   postPageWorkerClient($clienName2);    
    sleep(1200);
//Запрос на фриген для получения списка ип
    $HtmlBack = postPageApiBack($name);
    $HtmlBack1 = explode("<pre>", $HtmlBack);
    $HtmlBack2 = explode("<html>", $HtmlBack1[0]);
    $ipGoodBack = unserialize(base64_decode($HtmlBack2[0]));
    //$ipGoodBack[0] - реальные ип
    //$ipGoodBack[1] - пустоты
    //$ipGoodBack[2] - нуливые
    //$ipGoodBack[3] - Badlist
//Добавление обратки к новому списку
    setArrayAllProxy($ipGoodBack[0]);  //склеивание гудпрокси с ип хостинг
    $ipGoodNew = getArrayGoodProxy();
    $ipGoodNew = array_diff($ipGoodNew,$ipGoodBack[1],$ipGoodBack[2],$ipGoodBack[3]);
    echo "Before sort:" . count($ipGoodNew) . "\n";
    $ipGood = getUniqueIp($ipGoodNew);
    echo "After sort:" . count($ipGood) . "\n";
    echo "Send Array to API \n";
    $ipGood = base64_encode(serialize($ipGood));
    postPageApiFaucet($ipGood, $name);
    echo "Send...\n";
    echo date('H:i:s', time()) . "\n";
    echo "*****************************************************\n";
}
