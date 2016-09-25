<?php

require './FaucetProxy.php';
define('ROOT', dirname(__FILE__));
$worker = new GearmanWorker();
$worker->addServer();
/* Тут мы говорим, что готовы обработать ф-ю function_revert_string_and_caps, и что заниматься этим будет ф-я 'revCaps */
$worker->addFunction('proxyChecker', 'checkProxy');
/* Запускаем воркер. В таком варианте он отработает один раз */
// $worker->work();
/* А это вариант будет висеть демоном - есть на видео */
while ($worker->work()) {
    
};

//Ну и сама ф-я обработчик, аргумент один - объект-задание job
function checkProxy($job) {
    /* Извлекаем из job данные, переданные клиентом */
    $proxyIp = $job->workload();
    $FaucetObject = new Faucet('http://starbitco.in');
    $FaucetObject2 = new Faucet('http://infor.freeoda.com');

    $FaucetObject->getPage($proxyIp);
    $FaucetObject2->getPage($proxyIp);
    $result = $FaucetObject->page;
    $result2 = $FaucetObject2->page;
    if (stristr($result2['content'], 'DNS resolution error') == FALSE) {

        if ($result['http_code'] == 200 && $result['total_time'] <= 6) {

            if (stristr($result['content'], "Please complete the security check to access") == false) {
                if (stristr($result['content'], "api.solvemedia.com/papi/challenge.script") != false) {
                    $FaucetObject->setGoodProxy($proxyIp);
                }
            }
        }
    }
}
