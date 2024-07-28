<?php



class Requests
{


    public static function getApiUrl()
    {
        return "https://messengerg2c" . rand(1, 23) . ".iranlms.ir";
    }


    public static function getSocketUrl()
    {
        $urls = json_decode(file_get_contents("https://getdcmess.iranlms.ir/"), true)['data']['socket'];
        return $urls[array_rand($urls)];
    }


    public static function sendRequest($data)
    {
        $ch = curl_init(self::getApiUrl());

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_HTTPHEADER => [
                "Accept-Encoding: gzip",
                "Connection: Keep-Alive",
                "User-Agent: okhttp/3.12.1",
                "Referer: https://web.rubika.ir/",
                "Content-Type: application/json; charset=utf-8"
            ],
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);

        $response = curl_exec($ch);

        $error = curl_error($ch);
        curl_close($ch);

        return $error ? 'Error:' . $error : json_decode($response, true);
    }
}
