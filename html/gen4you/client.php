<?php
include 'FaucetProxy.php';
$client = new GearmanClient();

$file = 'iplist.txt';
$ipArray = array();
if (is_file($file)) {
    $f = fopen($file, "r");
       $count = 0;
       while ($line = fgets($f)) {
            
               if($count < 6000){
                  $ipArray[] = $line;
               }
           $count++;    
       }
}

/*Эта ф-я вернет true независимо от того, есть такой сервер или нет. Для проверки доступности сервера нужно использовать echo(‘’), установив на всякий случай таймаут в миллисекундах во избежание затыка скрипта при недоступности сервера */
$client->addServer();

$client->setTimeout(29000);
$faucetObject = new Faucet('http://ok.ru');
/*Отправляем задачу и данные на Gearman и ждем выполнения*/
foreach ($ipArray as $ip){
$task = $client->doBackground('proxyChecker', $ip);
echo "$task Address send to Gearman server ==> $ip<br>";
}
