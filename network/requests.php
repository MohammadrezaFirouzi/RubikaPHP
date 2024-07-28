<?php



class Requests
{


    public static function getApiUrl()
    {
        $urls = [
            "https://messengerg2c1.iranlms.ir",
            "https://messengerg2c2.iranlms.ir",
            "https://messengerg2c3.iranlms.ir",
            "https://messengerg2c4.iranlms.ir",
            "https://messengerg2c5.iranlms.ir",
            "https://messengerg2c6.iranlms.ir",
            "https://messengerg2c7.iranlms.ir",
            "https://messengerg2c8.iranlms.ir",
            "https://messengerg2c9.iranlms.ir",
            "https://messengerg2c10.iranlms.ir",
            "https://messengerg2c11.iranlms.ir",
            "https://messengerg2c12.iranlms.ir",
            "https://messengerg2c13.iranlms.ir",
            "https://messengerg2c14.iranlms.ir",
            "https://messengerg2c15.iranlms.ir",
            "https://messengerg2c16.iranlms.ir",
            "https://messengerg2c17.iranlms.ir",
            "https://messengerg2c18.iranlms.ir",
            "https://messengerg2c19.iranlms.ir",
            "https://messengerg2c20.iranlms.ir",
            "https://messengerg2c21.iranlms.ir",
            "https://messengerg2c22.iranlms.ir",
            "https://messengerg2c23.iranlms.ir"
        ];
        return $urls[array_rand($urls)];
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
