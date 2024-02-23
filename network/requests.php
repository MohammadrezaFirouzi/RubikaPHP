<?php 



class Requests{


    public static function getApiUrl()
    {
        $urls = json_decode(file_get_contents("https://getdcmess.iranlms.ir/"), true)['data']['API'];
        return $urls[array_rand($urls)];
    }


    public static function getSocketUrl()
    {
        $urls = json_decode(file_get_contents("https://getdcmess.iranlms.ir/"), true)['data']['socket'];
        return $urls[array_rand($urls)];
    }


    public static function sendRequest($data) {

        $head = array(
            "Accept-Encoding: gzip",
            "Connection: Keep-Alive",
            "User-Agent: okhttp/3.12.1",
            "Referer: https://web.rubika.ir/",
            "Content-Type: application/json; charset=utf-8"
        );
    
        while (true) { 
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::getApiUrl());
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            $result = json_decode($response, true);
    
            if ($status == 200) {
                return json_decode($response, true);
            } else {
                continue;
            }
        }


    }




}

?>