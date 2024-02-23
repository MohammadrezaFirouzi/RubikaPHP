<p align="center">
<a href='https://web.rubika.ir' target="_blank">
<img src='https://bahramali.ir/img/rubika.logo.svg'></img></a></p>
<br />
</p>

# کتابخانه روبیکا PHP 😅
<br/>



## اضافه کردن کتابخونه به پروژه 🎊 :
```php
<?php 

require_once('./rubika/rubika.php');

?>
```

<br>

## وارد کردن اطلاعات اکانت  :
```php
<?php 

require_once('./rubika/rubika.php');

$account = new Rubika($auth, $private_key);

?>


```


## استفاده از وب سوکت  :
```php
<?php 

require_once('./rubika/rubika.php');

$account = new Rubika($auth, $private_key);

// دریافت آپدیت های اکانت
$account->on_message(function (Message $update) use ($account) {
   
    print_r($update->text())

});
?>


```

## استفاده از فیلتر ها   :
```php
<?php 

require_once('./rubika/rubika.php');

$account = new Rubika($auth, $private_key);

// دریافت آپدیت های اکانت
$account->on_message(function (Message $update) use ($account) {
   
    if($update->is_group()){
        // گرفتن آپدیت های گروه
    }

    elseif($update->is_user()){
        // گرفتن آپدیت های کاربر 
    }

});
?>


```

## همگام سازی یک مثال دیگر:  :
```php
<?php 

require_once('./rubika/rubika.php');

$account = new Rubika($auth, $private_key);
// استفاده از متد برای گرفتن اطلاعات 
print_r($account->getChatInfo("CHAT_ID"));

?>


```

```php

$update->object_guid();

$update->chat_type();

$update->count_unseen();

$update->status();

$update->last_message_id();

$update->action();

$update->message_id();

$update->reply_message_id();

$update->text();

$update->is_edited();

$update->message_type();

$update->author_type();

$update->author_guid();

$update->prev_message_id();

$update->title();

$update->author_title();

$update->is_user();

$update->is_group();

$update->is_forward();

$update->forward_object_guid();

$update->forward_message_id();

$update->event_type();

$update->event_object_guid();

$update->file_inline();

$update->has_link();


```
## نمونه ربات ضد لینک  :
```php
<?php 

require_once('./rubika/rubika.php');

$account = new Rubika($auth, $private_key);

$account->on_message(function (Message $update) use ($account) {
    $pattern = '';
    if ($update->is_group() && preg_match("/(@|rubika|http)/", $update->text())) {
        // پاک کردن پیام
        $account->deleteMessage($update->object_guid() , $update->message_id());
        //اخراج کاربر از گروه  
        $account->banChatMember($update->object_guid() , $update->author_guid());
    }


});

?>


``` 
### نحوه دریافت Private key و  Auth  - 

[Download](https://github.com/MohammadrezaFirouzi/RubikaApiPHP/raw/main/video/rubika.mp4)


"# RubikaPHP" 
