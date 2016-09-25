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
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // таймаут соединения
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);        // таймаут ответа
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);       // останавливаться после 10-ого редиректа
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

function postPageApiCheck($name) {
    $ch = getConnection($name);
    curl_setopt($ch, CURLOPT_REFERER, $name);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "ApiBack=");
    $res = curl_exec($ch);
    //var_dump($res);
    curl_close($ch);
    return $res;
}

function getArrayGoodProxy() {

    $file = './components/goodProxy.txt';
    if (is_file($file)) {
        $f = fopen($file, "r");
        $i = 0;
        while ($i<1000) {
            $line = fgets($f);
            $ipGood[] = $line;
            $i++;
        }
    } else {
        echo "ERROR READ FILE";
    }
    return $ipGood;
}

$name = "http://123.1.1.240/ApiFaucet2.php";
//Запрос на фриген для получения списка ип
while (true) {
    $HtmlBack = postPageApiCheck($name);
    $HtmlBack1 = explode("Proxy Count:", $HtmlBack);
    $HtmlBack2 = explode("<br>", $HtmlBack1[1]);
    $ipCount = $HtmlBack2[0];

    if(6>$ipCount) {
        $ipGood = getArrayGoodProxy();
        echo " MANUAL Send Array to API \n";
        echo "IP:".count($ipGood)."\n";
        $ipGood = base64_encode(serialize($ipGood));
        postPageApiFaucet($ipGood,$name);
        echo "Send...\n";
        echo date('H:i:s',time())."\n";
        echo "*****************************************************\n";;
    
        
    } else {
        echo date('H:i:s', time()) . " IpGood:" . $ipCount . "\n";
        echo "*****************************************************\n";
    }
    
    sleep(60);
}

