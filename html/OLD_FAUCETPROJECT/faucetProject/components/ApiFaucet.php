<?php
define('ROOT', dirname(__FILE__));

if (isset($_POST['ApiFaucet']) && !empty($_POST['ApiFaucet'])) {

    $tmp = $_POST['ApiFaucet'];
    $goodProxyArray = unserialize(base64_decode($tmp));
    echo '<pre>';
    print_r($goodProxyArray);

    if (is_array($goodProxyArray)) {
        
        $result = implode("\n",$goodProxyArray);
            $file = 'goodProxy.txt';
            $f = fopen($file, "a+");
            flock($f, 2);
            fwrite($f, $result);
            flock($f, 3);
            fclose($f);
            
        
        echo "List saved";
    } else {
        echo "ERROR API POST REQUEST";
        return false;
    }
}
?>
<html>
    <meta>
    <title>API FAUCET</title>
    <body style="background: bisque">
        API FAUCET
    </body>
</html>

