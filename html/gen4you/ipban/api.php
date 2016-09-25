<?php
if(isset($_POST['banip']) && !empty($_POST['banip'])){
    $tmp = $_POST['banip'];
    //$banip - массив 
    $banip = unserialize(base64_decode($tmp));    
}


