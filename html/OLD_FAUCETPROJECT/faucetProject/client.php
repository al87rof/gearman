<?php
require './FaucetProxy.php';
define('ROOT', dirname(__FILE__));
$client = new GearmanClient();

/*Эта ф-я вернет true независимо от того, есть такой сервер или нет. Для проверки доступности сервера нужно использовать echo(‘’), установив на всякий случай таймаут в миллисекундах во избежание затыка скрипта при недоступности сервера */
$client->addServer();

//$client->setCompleteCallback('complete');
$client->setTimeout(29000);

/*true/false в зависимости от доступности сервера*/
//$haveGoodServer = $client->echo('');
//var_dump($haveGoodServer);

$faucetObject = new Faucet('http://ok.ru');
$iplist = $faucetObject->ipArray;

/*Отправляем задачу и данные на Gearman и ждем выполнения*/
foreach ($iplist as $ip){
$task = $client->doBackground('proxyChecker', $ip);
echo "$task Address send to Gearman server ==> $ip<br>";
}
/*
function complete($task){
    echo "The end $task->data()";
}
$client->runTasks();*/

/*Мы увидим результат, как только его вернет сервер, ну или выскочим по таймауту*/
