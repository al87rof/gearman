<?php

/**
 * Description of Joker
 *
 * @author Sanja
 */
class Joker extends AFaucet {
    
    /*
     * Метод postPage() принимает параметры:
     * name_text_vallet - имя поля ввода кошелька 
     * vallet_new -номер кошелька
     * count - счетчик кошелька 
     * Подготавливает и отправляет пост запрос на сервер
     */
    public function postPage($walletNameTextField,$wallet_new, $captcha1, $name_captcha_field, $captcha2, $name_captcha_field2, $proxyIp) {

        $ch = $this->getConnection($proxyIp);
        $name = $this->faucetName;
        $wallet = $wallet_new;
        curl_setopt($ch, CURLOPT_REFERER, $name);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$walletNameTextField=$wallet&$name_captcha_field=$captcha1&$name_captcha_field2=$captcha2");
        $content = curl_exec($ch);
        $this->page2 = $content;
        curl_close($ch);
    }
    
     /*
     * Метод парсит капчу с исходного кода страницы
     * возвращяет капчу в виде хтлм кода
     * или сообщение об ошибке
     */
    public function parseCaptcha(){

        $result = $this->page;

        if (($result['errno'] != 0 ) || ($result['http_code'] != 200)) {
            echo $result['errmsg'];
            return false;
        } else {
            $page = $result['content'];
            $chek = preg_match("~You can get a reward again~", $page);
            if ($chek != 1) {
                $res = explode("<center>", $page);
                $res2 = explode("<!-- PinkTussy Responsive -->", $res[1]);
                return $res2[0];
            } else {
                return "Сaptcha error <br>";
            }
        }
    }
    
    /*
     * возвращяет имя поля кошелька
     */
    public function parseNameTextFieldWallet() {
        return 'username';
    }
    
    /*
     * Метод проверки капчи
     * возвращяет результат
     */
    public function parseCaptchaValid() {
        $page = $this->page2;
        $res = explode("<div class=\"alert alert-danger\">", $page);
        
        if(!isset($res[1])){
            $res_1 = explode(" <div class=\"alert alert-success\">", $page);
            $res_2 = explode("<a target=\"_blank\"", $res_1[1]);
            return $res_2[0];
        }
        
        $res2 = explode("</div>			<div align=\"center\">", $res[1]);
        return $res2[0];
    }
}
