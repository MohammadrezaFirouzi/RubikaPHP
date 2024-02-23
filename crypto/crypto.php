<?php



class Crypto {
    const IV = "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00";

    private $key;
    private $auth;

    public function __construct($auth) {

        $this->key = $this->secret($auth);
        $this->auth = $auth;
    }

    private function secret($auth) {
        if (strlen($auth) != 32) {
            throw new Exception('the length of auth must be 32 digits');
        }

        $result = '';
        $chunks = str_split($auth, 8); 
        $sumchar = $chunks[2] . $chunks[0] . $chunks[3] . $chunks[1];

        for ($i = 0; $i < strlen($sumchar); $i++) { 
            $c = $sumchar[$i];

            if ($c >= '0' && $c <= '9') {
                $result .= chr((ord($c) - 48 + 5) % 10 + 48);

            } else {
                $result .= chr((ord($c) - 97 + 9) % 26 + 97);
            }
        }
        return $result;
    }

    public static function decode_auth($auth) {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = strtoupper($lowercase);
        $digits = '0123456789';
        $result = '';

        for ($i = 0; $i < strlen($auth); $i++) {
            $s = $auth[$i];
            
            if (strpos($lowercase, $s) !== false) {
                $result .= chr(((32 - (ord($s) - 97)) % 26) + 97);

            } elseif (strpos($uppercase, $s) !== false) {
                $result .= chr(((29 - (ord($s) - 65)) % 26) + 65);

            } elseif (strpos($digits, $s) !== false) {
                $result .= chr(((13 - (ord($s) - 48)) % 10) + 48);

            } else {
                $result .= $s;
            }
        }
        
        return $result;
    }

    public static function random_tmp($length) {
        $result = '';
        $characters = 'abcdefghijklmnopqrstuvwxyz';

        for ($i = 0; $i < $length; $i++) {
            $result .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $result;
    }

    public static function generate_keys() {
        $config = array(
            'digest_alg' => 'sha256',
            'private_key_bits' => 1024,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        );

        $result = openssl_pkey_new($config);

        openssl_pkey_export($result, $private_key);

        $publickey = openssl_pkey_get_details($result)['key'];

        $public_key = self::decode_auth(base64_encode($publickey));

        return array($public_key, $private_key);
    }

    public function encrypt($data) {
        if (!is_string($data)) {
            $data = json_encode($data);
        }

        $padding = 16 - (strlen($data) % 16);
        $data .= str_repeat(chr($padding), $padding);

        $cipher = openssl_encrypt($data, 'aes-256-cbc', $this->key,
        OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, self::IV);

        return base64_encode($cipher);
    }

    public function decrypt($data) {
        $decoded_data = base64_decode($data);

        $decipher = openssl_decrypt($decoded_data, 'aes-256-cbc', $this->key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, self::IV);

        $padding = ord($decipher[strlen($decipher) - 1]);
        $result = substr($decipher, 0, -$padding);

        return $result;
    }

    public static function sign($private_key, $data) {
        $key = openssl_pkey_get_private($private_key);

        openssl_sign($data, $signature, $key, OPENSSL_ALGO_SHA256);

        return base64_encode($signature);
    }

    public static function decrypt_rsa_oaep($private_key, $data) {
        $raw_data = base64_decode($data);

        $private_key = openssl_pkey_get_private($private_key);

        openssl_private_decrypt($raw_data, $decrypted, $private_key, OPENSSL_PKCS1_OAEP_PADDING);

        return $decrypted;
    }
}

?>
