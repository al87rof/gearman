<?php
/**
 * Description of Satoshiclaims
 *
 * @author Sanja
 */
class Satoshiclaims extends AFaucet{
    
    /*
     * Метод postPage() принимает параметры:
     * name_text_vallet - имя поля ввода кошелька 
     * vallet_new -номер кошелька
     * count - счетчик кошелька 
     * Подготавливает и отправляет пост запрос на сервер
     */
   
    public function postPage($walletNameTextField, $wallet_new, $captcha1, $name_captcha_field, $captcha2, $name_captcha_field2, $proxyIp) {

        $ch = $this->getConnection($proxyIp);
        $name = $this->faucetName;
        $adsress = '';
        curl_setopt($ch, CURLOPT_REFERER, $name);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "address=$adsress&$walletNameTextField=$wallet_new&$name_captcha_field=$captcha1&$name_captcha_field2=$captcha2");
        $content = curl_exec($ch);
        $this->page2 = $content;
        curl_close($ch);
    }
    
     /*
     * Метод парсит капчу с исходного кода страницы
     * возвращяет капчу в виде хтлм кода
     * или сообщение об ошибке
     */

    public function parseCaptcha() {

        $result = $this->page;

        if (($result['errno'] != 0 ) || ($result['http_code'] != 200)) {
            echo $result['errmsg'];
            return false;
        } else {
            $page = $result['content'];


            $res = explode("<div class=\"form-group\">", $page);
            $res2 = explode("<div class=\"text-center\">", $res[3]);
            return $res2[0];
        }
    }
    
     /*
     * Метод парсит имя поля кошелька с исходного кода страницы
     * возвращяет имя поля кошелька
     * или сообщение об ошибке
     */

    public function parseNameTextFieldWallet() {

        $result = $this->page;

        if (($result['errno'] != 0 ) || ($result['http_code'] != 200)) {
            echo $result['errmsg'];
            return false;
        } else {
            $page = $result['content'];

            $res = explode("<div class=\"col-sm-8 col-md-7\" style=\"min-width: 270px;\">", $page);
            $res2 = explode("\" class=\"form-control", $res[1]);
            $res3 = explode("<input type=\"text\" name=\"", $res2[0]);
            return $res3[1];
        }
    }
    
     /*
     * Метод проверки капчи
     * возвращяет результат
     */

    public function parseCaptchaValid() {
        $page = $this->page2;
       // echo $page;
        //exit;
        $res = explode("<p class=\"alert alert-danger\">", $page); 
        //  print_r($res);
        //  exit;
      //  $res = explode("<div class=\"alert alert-danger\">", $page);     
        
        /*if(isset($res22[1])){
            $res_1 = explode("<div class=\"alert alert-danger\">", $page);
            $res_2 = explode("</div>", $res_1[1]);
            return $res_2[0];    
        }*/
      
     
        if (!isset($res[2])) {
            $res_1 = explode("<div class=\"alert alert-success\">", $page);
            $res_2 = explode("<a target=", $res_1[1]);
            return $res_2[0];
        }
        
        $res2 = explode("</p>", $res[2]);
        return $res2[0];
    }

}
