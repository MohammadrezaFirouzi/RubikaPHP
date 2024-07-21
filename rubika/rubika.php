
<?php

require_once('./vendor/autoload.php');
require_once('./network/requests.php');
require_once('./crypto/crypto.php');
require_once('./message/message.php');

use Ratchet\Client\WebSocket;
use Ratchet\Client\Connector;

class Rubika
{
    private $auth;
    private $auth_dec;
    private $private_key;
    private $url;
    private $crypto;
    private $thumb_inline;

    private $client = [
        "app_name" => "Main",
        "app_version" => "4.4.6",
        "platform" => "Web",
        "package" => "web.rubika.ir",
        "lang_code" => "fa"
    ];

    public function __construct(string $auth, string $private_key)
    {
        $this->auth_dec = Crypto::decode_auth($auth);
        $this->private_key = json_decode(base64_decode($private_key),true)['d'];
        $this->crypto = new Crypto($this->auth_dec);
        $this->url = 'wss://nsocket7.iranlms.ir:80';
        $this->auth = $auth;
        $this->thumb_inline = "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";
    }

    private function getState() {
        return time() - 150;
    }
    private function run($input = [] , string $method){
        $input = $this->crypto->encrypt([
            "method" => $method,
            "input" => $input,
            "client" => $this->client
        ]);
        $signature = Crypto::sign($this->private_key,  $input);
        $data = [
            "api_version" => "6",
            "auth" => $this->auth,
            "data_enc" => $input,
            "sign" => $signature,
        ];

        $response = Requests::sendRequest($data);
        $result = json_decode($this->crypto->decrypt($response['data_enc']), true);
        return $result['data'];

    }

    public function requestSendFile($file_name, $size, $mime)
    {

        return self::run([
                "file_name" => $file_name,
                "size" => $size,
                "mime" => $mime,
        ],"requestSendFile");
    }

    public function upload($file)
    {

        $pr = self::requestSendFile($file, filesize($file), pathinfo($file, PATHINFO_EXTENSION));

        $chunk_size = 131072;
        $file_content = file_get_contents($file);
        $total_parts = (int) ceil(strlen($file_content) / $chunk_size);

        for ($part_number = 1; $part_number <= $total_parts; $part_number++) {
            $start = ($part_number - 1) * $chunk_size;
            $end = min($part_number * $chunk_size, strlen($file_content));
            $data = substr($file_content, $start, $end - $start);
            $headers = array(
                'Host: ' . str_replace("/UploadFile.ashx", "", str_replace("https://", "", $pr['upload_url'])),
                'Connection: keep-alive',
                'Content-Length: ' . strval(strlen($data)),
                'auth: ' . $this->auth_dec,
                'file-id: ' . $pr["id"],
                'total-part: ' . strval($total_parts),
                'sec-ch-ua: "Not_A Brand";v="8", "Chromium";v="120", "Google Chrome";v="120"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Windows"',
                'Accept: application/json, text/plain, */*',
                'part-number: ' . strval($part_number),
                'chunk-size: ' . strval(strlen($data)),
                'access-hash-send: ' . $pr["access_hash_send"],
                'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'origin: https://web.rubika.ir',
                'referer: https://web.rubika.ir/'
            );

            $options = array(
                CURLOPT_URL => $pr['upload_url'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => $headers
            );

            $curl = curl_init();
            curl_setopt_array($curl, $options);
            $response = json_decode(curl_exec($curl), true);
            curl_close($curl);
            if ($response['data'] != null) {
                return array(
                    'dc_id' => $pr['dc_id'],
                    'file_id' => $pr['id'],
                    'file_name' => $file,
                    'size' => filesize($file),
                    'mime' => pathinfo($file, PATHINFO_EXTENSION),
                    'access_hash_rec' => $response['data']['access_hash_rec']
                );
            }
        }
    }
    private function startsWith($string, $prefix)
    {
        return substr($string, 0, strlen($prefix)) === $prefix;
    }


    public function get_contacts($start_id = null)
    {
        return self::run([
            "start_id" => $start_id
        ],"getContacts");
    }

    public function getChats($start_id = null)
    {
        return self::run([
            "start_id" => $start_id
        ],"getChats");
    }

    public function getMessagesUpdates($object_guid , $state)
    {
        return self::run([
            "object_guid" => $object_guid,
            "state"=>$state,
        ],"getMessagesUpdates");
    }

    public function getMessagesInterval($object_guid , $middle_message_id)
    {
        return self::run([
            "object_guid" => $object_guid,
            "middle_message_id"=>$middle_message_id
        ],"getMessagesInterval");
    }

    public function sendTextMessage($object_guid, $text, $metadata = null, $reply_to_message_id = null)
    {
        return self::run([
            "object_guid" => $object_guid,
            "rnd" => rand(100000, 999999999),
            "text" => $text,
            "metadata" => $metadata,
            "reply_to_message_id" => $reply_to_message_id
        ], "sendMessage");
    }


    public function getMessages($object_guid, $max_id)
    {
        return self::run([
            "object_guid" => $object_guid,
            "sort" => "FromMax",
            "filter_type" => "Media",
            "max_id" => $max_id
        ],"getMessages");
    }

    public function addContact($first_name, $last_name, $phone)
    {
        return self::run([
            "first_name" => $first_name,
            "last_name" => $last_name,
            "phone" => $phone,
        ],"addAddressBook");
    }

    public function addGroup($title, $member_guids)
    {
        return self::run([
            "title" => $title,
            "member_guids" => $member_guids,
        ], "addGroup");
    }

    public function addChannel($title, $channelType = "Public", $users_chat_id = null)
    {
        return self::run([
            "channel_type" => $channelType,
            "title" => $title,
            "member_guids" => $users_chat_id,
        ], "addChannel");
    }

    public function getChatInfo($chat_id)
    {

        if (self::startsWith($chat_id, "g0")) {
            $method = "getGroupInfo";
            $chat_type = "group_guid";
        } elseif (self::startsWith($chat_id, "c0")) {
            $method = "getChannelInfo";
            $chat_type = "channel_guid";
        }elseif(self::startsWith($chat_id, "u0")){
            $method = "getUserInfo";
            $chat_type = "user_guid";
        }
        return self::run([
            $chat_type => $chat_id,
        ], $method);
    }


    public function banChatMember($chat_id, $member_guid)
    {

        if (self::startsWith($chat_id, "g0")) {
            $method = "banGroupMember";
            $chat_type = "group_guid";
        } elseif (self::startsWith($chat_id, "c0")) {
            $method = "banChannelMember";
            $chat_type = "channel_guid";
        }
        return self::run([
            $chat_type => $chat_id,
            "member_guid" => $member_guid,
            "action" => "Set"
        ], $method);
    }

    public function getMembers($chat_id, $search_text = null , $start_id = null)
    {

        if (self::startsWith($chat_id, "g0")) {
            $method = "getGroupAllMembers";
            $chat_type = "group_guid";
        } elseif (self::startsWith($chat_id, "c0")) {
            $method = "getChannelAllMembers";
            $chat_type = "channel_guid";
        }
        return self::run([
            $chat_type => $chat_id,
            "search_text"=>$search_text,
            "start_id" => $start_id,

        ], $method);
    }

    public function unbanChatMember($chat_id, $member_guid)
    {

        if (self::startsWith($chat_id, "g0")) {
            $method = "banGroupMember";
            $chat_type = "group_guid";
        } elseif (self::startsWith($chat_id, "c0")) {
            $method = "banChannelMember";
            $chat_type = "channel_guid";
        }
        return self::run([
            $chat_type => $chat_id,
            "member_guid" => $member_guid,
            "action" => "Unset"
        ], $method);
    }

    public function invite($chat_id, $member_guid)
    {

        if (self::startsWith($chat_id, "g0")) {
            $method = "addGroupMembers";
            $chat_type = "group_guid";
        } elseif (self::startsWith($chat_id, "c0")) {
            $method = "addChannelMembers";
            $chat_type = "channel_guid";
        }
        return self::run([
            $chat_type => $chat_id,
            "member_guids" => $member_guid,
        ],$method);
    }

    public function block($member_guid)
    {
        return self::run([
            "action" => "Block",
            "user_guid" => $member_guid,
        ], "setBlockUser");
    }


    public function unblock($member_guid)
    {
        return self::run([
            "action" => "Unblock",
            "user_guid" => $member_guid,
        ], "setBlockUser");
    }

    public function changeLink($chat_id)
    {

        if (self::startsWith($chat_id, "g0")) {
            $method = "setGroupLink";
            $chat_type = "group_guid";
        } elseif (self::startsWith($chat_id, "c0")) {
            $method = "setChannelLink";
            $chat_type = "channel_guid";
        }
        return self::run([
            $chat_type => $chat_id,
        ], $method);
    }

    public function deleteContact($member_guid)
    {
        return self::run([
            "user_guid" => $member_guid,
        ], "deleteContact");
    }

    public function deleteMessage($chat_id, $message_ids)
    {
        return self::run([
            "object_guid" => $chat_id,
            "message_ids" => $message_ids,
            "type" => "Global"
        ],"deleteMessages");
    }

    public function send_file($chat_id, $file_name, $caption = null, $metadata = null, $reply_to_message_id = null)
    {
        $up = self::upload($file_name);
        return self::run([
            "object_guid" => $chat_id,
            "rnd" => rand(100000, 999999999),
            "file_inline" => [
                "dc_id" => $up['dc_id'],
                "file_id" => $up['file_id'],
                "type" => "File",
                "file_name" => $up['file_name'],
                "size" => $up['size'],
                "mime" => $up['mime'],
                "metadata" => $metadata,

                "access_hash_rec" => $up['access_hash_rec'],
            ],
            "text" => $caption,
        ],"sendMessage");
    }

    public function send_Gif($chat_id, $file_name, $caption = null, $metadata = null, $reply_to_message_id = null)
    {
        $up = self::upload($file_name);
        return self::run([
            "object_guid" => $chat_id,
            "rnd" => rand(100000, 999999999),
            "file_inline" => [
                "dc_id" => $up['dc_id'],
                "file_id" => $up['file_id'],
                "type" => "Gif",
                "file_name" => $up['file_name'],
                "size" => $up['size'],
                "mime" => $up['mime'],
                "thumb_inline" => $this->thumb_inline,
                "width" => 512,
                "height" => 512,
                "access_hash_rec" => $up['access_hash_rec'],
                "time" => 900
            ],
            "text" => $caption,
            "reply_to_message_id" => $reply_to_message_id
        ], "sendMessage");
    }

    public function send_photo($chat_id, $file_name, $caption = null, $metadata = null, $reply_to_message_id = null)
    {
        $up = self::upload($file_name);
        $imageData = file_get_contents($file_name);
        $base64Image = base64_encode($imageData);
        return self::run([
            "object_guid" => $chat_id,
            "rnd" => rand(100000, 999999999),
            "file_inline" => [
                "dc_id" => $up['dc_id'],
                "file_id" => $up['file_id'],
                "type" => "Image",
                "file_name" => $up['file_name'],
                "size" => $up['size'],
                "mime" => $up['mime'],
                "thumb_inline" => $base64Image,
                "width" => 512,
                "height" => 512,
                "access_hash_rec" => $up['access_hash_rec'],
            ],
            "text" => $caption,
            "reply_to_message_id" => $reply_to_message_id
        ], "sendMessage");
    }

    public function send_Video($chat_id, $file_name, $photo_tumb,$caption = null, $reply_to_message_id = null)
    {
        $up = self::upload($file_name);
        $imageData = file_get_contents($photo_tumb);
        $base64Image = base64_encode($imageData);
        return self::run([
            "object_guid" => $chat_id,
            "rnd" => rand(100000, 999999999),
            "file_inline" => [
                "dc_id" => $up['dc_id'],
                "file_id" => $up['file_id'],
                "type" => "Video",
                "file_name" => $up['file_name'],
                "size" => $up['size'],
                "mime" => $up['mime'],
                "thumb_inline" => $base64Image,
                "width" => 512,
                "height" => 512,
                "time"=>"2",
                "access_hash_rec" => $up['access_hash_rec'],
            ],
            "text" => $caption,
            "reply_to_message_id" => $reply_to_message_id
        ], "sendMessage");
    }

    public function send_Music($chat_id, $file_name, $caption = null, $reply_to_message_id = null)
    {
        $up = self::upload($file_name);
        return self::run([
            "object_guid" => $chat_id,
            "rnd" => rand(100000, 999999999),
            "file_inline" => [
                "dc_id" => $up['dc_id'],
                "file_id" => $up['file_id'],
                "type" => "Music",
                "file_name" => $up['file_name'],
                "size" => $up['size'],
                "mime" => $up['mime'],
                "thumb_inline" => $this->thumb_inline,
                "width" => 0,
                "height" => 0,
                "music_performer"=>"PHP Library",
                "is_round"=>false,
                "time"=>"2",
                "access_hash_rec" => $up['access_hash_rec'],
            ],
            "is_mute"=>false,
            "text" => $caption,
            "reply_to_message_id" => $reply_to_message_id
        ], "sendMessage");
    }

    public function send_Voice($chat_id, $file_name, $caption = null, $reply_to_message_id = null)
    {
        $up = self::upload($file_name);
        return self::run([
            "object_guid" => $chat_id,
            "rnd" => rand(100000, 999999999),
            "file_inline" => [
                "dc_id" => $up['dc_id'],
                "file_id" => $up['file_id'],
                "type" => "Voice",
                "file_name" => $up['file_name'],
                "size" => $up['size'],
                "mime" => $up['mime'],
                "time"=>"2",
                "access_hash_rec" => $up['access_hash_rec'],
            ],
            "text" => $caption,
            "reply_to_message_id" => $reply_to_message_id
        ], "sendMessage");
    }

    public function getChatUpdates(){
        return self::run([
            "state" => $this->getState(),
        ], "getChatsUpdates");
    }

    public function on_message(callable $callback)
    {   
        self::getChatUpdates();
        $loop = React\EventLoop\Loop::get();
        $connector = new Connector($loop);
        $connector($this->url, [], [])
            ->then(function (WebSocket $conn) use ($callback, $loop) {
                echo "Connected\n";
                $timer = $loop->addPeriodicTimer(30, function () use ($conn) {
                    self::getChatUpdates();
                    $conn->send(json_encode(['']));
                });

                $conn->send(json_encode(["api_version" => "6", "auth" => $this->auth_dec, "method" => "handShake"]));

                $conn->on('message', function ($msg) use ($conn, $callback) {

                    $data = json_decode($msg, true);
                    if (isset($data['type'])) {
                        $update = json_decode($this->crypto->decrypt($data['data_enc']),true);
                        if (isset($update["chat_updates"])) {
                            if (isset($update['message_updates'])) {
                                $callback(new Message($update));
                            }
                        }
                    }
                });

                $conn->on('close', function ($code, $reason) use ($timer, $callback) {
                    $this->on_message($callback);
                });
            }, function (Exception $e) use ($callback) {
                echo "Error Connect WebSocket";
            });

        $loop->run();
    }

    
}



?>
