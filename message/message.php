<?php


class Message
{
    private $data;
    private $auth;
    private $private_key;
    private $bot;
    private $api;

    public function __construct($data, $api)
    {
        $this->data = $data;
        $this->api = $api;
    }

    public function object_guid(): ?string {
        return $this->data["chat_updates"][0]["object_guid"] ?? '';
    }

    public function message_dict(){
        return $this->data['message_updates'][0] ?? '';
    }

    public function chat_type(): ?string {
        return $this->data["chat_updates"][0]["type"] ?? '';
    }

    public function count_unseen(): int {
        return intval($this->data["chat_updates"][0]["chat"]["count_unseen"] ?? 0);
    }

    public function status(): ?string {
        return $this->data["chat_updates"][0]["chat"]["status"] ?? '';
    }

    public function last_message_id(): ?string {
        return $this->data["chat_updates"][0]["chat"]["last_message_id"] ?? '';
    }

    public function action(): ?string {
        return $this->data["message_updates"][0]["action"] ?? '';
    }

    public function message_id(): ?string {
        return $this->data["message_updates"][0]["message_id"] ?? '';
    }

    public function reply_message_id(): ?string {
        return $this->data["message_updates"][0]["message"]["reply_to_message_id"] ?? '';
    }

    public function text(): ?string {
        return isset($this->data["message_updates"][0]["message"]["text"]) ? strval($this->data["message_updates"][0]["message"]["text"]) : '';
    }

    public function is_edited(): bool {
        return isset($this->data["message_updates"][0]["message"]["is_edited"]) ? $this->data["message_updates"][0]["message"]["is_edited"] : false;
    }

    public function message_type(): ?string {
        if ($this->file_inline()) {
            return $this->file_inline()["type"] ?? '';
        }
        return $this->data["message_updates"][0]["message"]["type"] ?? '';
    }

    public function author_type(): ?string {
        return $this->data["message_updates"][0]["message"]["author_type"] ?? '';
    }

    public function author_guid(): ?string {
        return $this->data["message_updates"][0]["message"]["author_object_guid"] ?? '';
    }

    public function prev_message_id(): ?string {
        return $this->data["message_updates"][0]["prev_message_id"] ?? '';
    }

    public function title(): ?string {
        return $this->data['show_notifications'][0]["title"] ?? '';
    }

    public function author_title(): ?string {
        return $this->data['chat_updates'][0]['chat']['last_message']["author_title"] ?? $this->title();
    }

    public function is_user(): bool {
        return $this->chat_type() === "User";
    }

    public function is_group(): bool {
        return $this->chat_type() === "Group";
    }

    public function is_forward(): bool {
        return isset($this->data["message_updates"][0]["message"]["forwarded_from"]);
    }

    public function forward_from(): ?string {
        return $this->is_forward() ? ($this->data["message_updates"][0]["message"]["forwarded_from"]["type_from"] ?? '') : '';
    }

    public function forward_object_guid(): ?string {
        return $this->is_forward() ? ($this->data["message_updates"][0]["message"]["forwarded_from"]["object_guid"] ?? '') : '';
    }

    public function forward_message_id(): ?string {
        return $this->is_forward() ? ($this->data["message_updates"][0]["message"]["forwarded_from"]["message_id"] ?? '') : '';
    }

    public function is_event(): bool {
        return isset($this->data["message_updates"][0]["message"]["event_data"]);
    }

    public function event_type(): ?string {
        return $this->is_event() ? ($this->data["message_updates"][0]["message"]["event_data"]["type"] ?? '') : '';
    }

    public function event_object_guid(): ?string {
        return $this->is_event() ? ($this->data["message_updates"][0]["message"]["event_data"]["performer_object"]["object_guid"] ?? '') : '';
    }

    public function file_inline(): ?array {
        return $this->data["message_updates"][0]["message"]["file_inline"] ?? null;
    }

    public function reply($text)
    {
        $this->api->sendTextMessage($this->object_guid(), $text, null, $this->message_id());
    }

    public function has_link(): bool
    {

        $links = ["http:/", "https:/", "www.", ".ir", ".com", ".net", "@"];
        foreach ($links as $link) {
            if (stripos($this->text(), $link) !== false) {
                return true;
            }
        }
        return false;
    }
}
