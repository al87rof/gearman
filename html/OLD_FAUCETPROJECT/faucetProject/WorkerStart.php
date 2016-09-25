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
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1); // таймаут соединения
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);        // таймаут ответа
    curl_setopt($ch, CURLOPT_MAXREDIRS, 1);       // останавливаться после 10-ого редиректа
    curl_setopt($ch, CURLOPT_COOKIEFILE, "my_cookies.txt"); //Создание файла куков
    curl_setopt($ch, CURLOPT_COOKIEJAR, "my_cookies.txt");
    return $ch;
}


function postPageWorkerClient($name) {
    $ch = getConnection($name);
    curl_setopt($ch, CURLOPT_REFERER, $name);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "ApiFaucet=s");
    $res = curl_exec($ch);
    curl_close($ch);
}





$name = "http://freegen.tk/ApiFaucet2.php";
$WorkerName = "http://localhost/faucetProject/worker.php";
$clienName = "http://localhost/faucetProject/client.php";

//Запуск Воркеров 100 штук
while(true){
for($i=0;$i<45;$i++){
    postPageWorkerClient($WorkerName);
}
echo "Worker start \n";
echo date('H:i:s',time())."\n";
echo "_____________________\n";
sleep(909080);
}
