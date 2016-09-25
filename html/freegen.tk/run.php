<html>
 <meta name="referrer" content="never">
 <body>
<?php
//Подключаем нужные файлы и создаем константы 
define('ROOT', dirname(__FILE__));
include  ROOT.'\classes\AFaucet.php';
require  ROOT.'\classes\Hoard.php';
require ROOT.'\classes\Joker.php';
require ROOT.'\classes\Pinktussy.php';
require ROOT.'\classes\FreeBtCoins.php';
require ROOT.'\classes\Coins4america.php';
require ROOT.'\classes\Satoshiclaims.php';
require ROOT.'\classes\Mydailybtc.php';
require ROOT.'\classes\Prostoloto.php';
require ROOT.'\classes\Aquabitcoin.php';
require ROOT.'\classes\Qcrazy.php';
$wlArray = require ROOT.'\components\FaucetList.php';

//Созданием обьекты партнеров Faucet
$hoardFaucet =       new Hoard("http://satoshihoard.com/");//
$jokerFaucet =       new Joker("http://faucet.jokertimes.co/");//
$freeBtCoinsFaucet = new FreeBtCoins("http://freebtcoins.com/");
$satoshiclaims = new Satoshiclaims("http://satoshiclaims.com/");
$pinktussy = new Pinktussy("http://faucet.pinktussy.co/");
$coins4america = new Coins4america("http://coins4america.com/");
$mydalybtc = new Mydailybtc("http://mydailybtc.com/");
$prostoloto = new Prostoloto("http://prostoloto.ru/");
$aquabitcoin  = new Aquabitcoin("http://aquabitcoin.com/");
$qcrazy  = new Qcrazy("http://q-crazy-fire-p.dp.ua/");
//Создаем Массив обьектов
$arrayObjectFaucet = array();
//$arrayObjectFaucet[] = $hoardFaucet;
//$arrayObjectFaucet[] = $jokerFaucet;
//$arrayObjectFaucet[] = $freeBtCoinsFaucet;  
//$arrayObjectFaucet[] =$satoshiclaims;
//$arrayObjectFaucet[] = $pinktussy;
//$arrayObjectFaucet[] = $coins4america;
//$arrayObjectFaucet[] = $mydalybtc;
//$arrayObjectFaucet[] = $prostoloto;
//$arrayObjectFaucet[] = $aquabitcoin;
 $arrayObjectFaucet[] = $qcrazy;       
//Получяем обьект Tmp для вызова счетчиков (Эффект полиморфизма)
$polymObjectTmp = $arrayObjectFaucet[0];
//Полуяем счетчик кошелька
$wlCount = $polymObjectTmp->getWalletCount();
//Получяем счетчик обьекта
$obCount = $polymObjectTmp->getObjectCount();
//Делаем проверку или не кончились кошельки.если кончились обнуляем счетчик       
 if($wlCount >= 20){
         $wlCount=0;
    }
//Делаем проверку или не кончились обьекты в масиве
if($obCount > count($arrayObjectFaucet)-1){
    $obCount = 0;
    $wlCount++;
}
//Полуяем кошелек    
$wallet = $wlArray[$wlCount];
//Получяем обьект (Эффект полиморфизма)
$polymObject = $arrayObjectFaucet[$obCount];
//вывод для отладки
echo "Count Wallet $wlCount <br>";
echo "Count Object $obCount <br>";
echo "$wallet <br>";
echo "<br>";
var_dump($polymObject);
//проверка на нажатие кнопки Submit
if(!isset($_POST['adcopy_response']) && empty($_POST['adcopy_response'])){
//Метод читает файл goodProxy.txt в масив ipGood
$polymObject->getArrayGoodProxy();
//Ищем в массиве первый рабочий Proxy
$ip = $polymObject->sortArray();
//Отправляем первый курл запрос на сайт партнера  
$polymObject->getPage($ip);
//Получяем результат запроса в виде Массива
//Исходны код страницы в хранится в $pageHtml['content']
$pageHtml = $polymObject->page;
//Выводим статистику по Proxy (Это можно удалить или закоментировать для отладки)
//-------------------------------------------------------------
    echo 'Total time: ' . $pageHtml['total_time'];
    echo '<br>';
    echo 'Ip Address: ' . $pageHtml['primary_ip'];
    echo '<br>';
    echo 'Speed bps: ' . $pageHtml['speed_download'];
//--------------------------------------------------------------    
//Парсим капчу
$captcha = $polymObject->parseCaptcha();
$nameWallet = $polymObject->parseNameTextFieldWallet();
//-------------------------------------
}
else{
//Собираем переменные которые нужны для пост транзакции     
    $value1 = $_POST['adcopy_response'];          // Поле captcha
    $key1 = 'adcopy_response';        // Поле captcha
    $value2 = $_POST['adcopy_challenge'];         // Поле captcha
    $key2 = 'adcopy_challenge';      // Поле captcha
    $wallet = $_POST['wallet'];                     // Кошелек
    $nameWallet = $_POST['nameWallet'];
    $ip = $_POST['ip'];                             // Прокси ИП      
//Отправляем второй курл запрос на сервер 
    $polymObject->postPage($nameWallet,$wallet, $value1, $key1, $value2, $key2,$ip);    
    echo $result = $polymObject->parseCaptchaValid();
//Увеличиваем счетчик кошелька и перезаписываем его
    
    if($result != 'Wrong captcha, try again!' && $result != 'Invalid captcha code!' /*&& $result != 'Insufficient funds.'*/){
        $obCount++;
        $polymObject->setWalletCount($wlCount);
        $polymObject->setObjectCount($obCount);
    }
    echo '<form method="post" action="run.php">
    <center> 
        <input name="but" value="Submit!" type="submit"><br>
    </center>
</form>';
    exit();
}

?>
<form method="post" action="run.php">
    <center>
<?php echo $captcha;?>        
        Input BTC Vallet   <input type="text" name="wallet" value="<?php echo $wallet;?>"><br>
     <input type="text" name="ip" value="<?php echo $ip;?>"><br>     
     <input type="text" name="nameWallet" value="<?php echo $nameWallet;?>"><br>
        <input name="but" value="Submit!" type="submit"><br>
    </center>
</form>
     </body>
</html>