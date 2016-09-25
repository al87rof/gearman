<?php

function getProxyList(){
    $url = "http://fineproxy.org/freshproxy/#more-6";
    $list = file_get_contents($url);
    return $list;
}

function parseProxyList($list){
    $res = explode("<div class=\"entry-content\">", $list);
    $res2 =  explode("</div>", $res[1]);
    return $res2[0];
}

while(true){
 $list = strip_tags(parseProxyList(getProxyList()));
 $file = "iplist.txt";
 $f = fopen($file, "w+");
 fwrite($f, $list);
 fclose($f);
 echo "List load sucsess! ".date('H:i:s', time()) . "\n";
 sleep(7200);
}