<?php


require_once('./rubika/rubika.php');
require_once('./crypto/crypto.php');


$private_key = "eyJ2ZXJzaW9uIjoiNiIsImQiOiItLS0tLUJFR0lOIFJTQSBQUklWQVRFIEtFWS0tLS0tXG5NSUlDWEFJQkFBS0JnUUNCelBlQUs2U29nUHJLWlVWaXhTd2xDODFsdkFPVkJXRThJeEI3b2FIRGdrcERCNTdJXG5VdGYwb0llV3hxTWNIdlVBL0ljWFpBTEZtVUxjMjBQUDJWb1hPZ2J0dTdJeHZWTGtla1BQbDcraEFwS0pkYXJaXG5hTm02ZnQ0d2JqbmxKRDRlNUFVNlo4TlIyM3BLTThSNnU2TjZkNDVWeGFVTDNHQTRYcy9Ub29md0h3SURBUUFCXG5Bb0dBQWJLRmRnYWNFNXdFSzR0aGVlWXNLcHZaNXIrcnFGSTJzRXVoRm96SmliMzFiS1QzM1pTL3dESXRNN3FMXG5QWDNtSkVvcEZoUTN1US9GQVJCWTlhblk5TG43Qm14M1BKWE5oTHNOd1ZlQ1ZJdXNpNVRYb3dsL1JpZTR3NitMXG5wazdZaGpTMzhKa1BxcUtoWTRic09keHA2NmNMUkdDbEgrME5nZUJyNFlPL3Vka0NRUURTOEVDK21PQkZ3MlFBXG5wNlkzNkowMkRXL2tDMnhLd256ZUlNam5GMzIyNW1wOUZGMVkyK1oweEF3c2UzU2o2Z2pvOHRud2RTekMxL2RBXG5vbXFJRjROckFrRUFuWWQ0UkpmMWMxajU0YmVHZnJTbFp6cFlMdnNyRDZqUnl1SUdXam9MM3pRa1dTZ0lFclZUXG5ha29QVmV2ampKWjVWWk9YVjBjQU5FbUxoUUlkbzNwbkhRSkFHQ1ZrWSswQUR6eFVvRGFRc21td1JWVzRielJYXG5peDlFUi9FY3prZEVIc0cxZ3VmbjM1b2NnVlZIeDNmQ1hGa1grQUtFckIvZHBkZ3U5M2tnRk1BTVRRSkJBSlRGXG55am5ONGN1TUxvS1QxdnQzRS9jSHpSeWhyU3RlM3JOaS8yamJCVGRKZ1VLS1lnVjVKa3h0b1VvZU80c2MyWDZPXG5veEdVUm9jYkpoNzV2cEFVRzZrQ1FBTUhqOTdnV1dYOVZUUkVSQ0lJekRyOTVjOEgraFZsZjBtamJ4OEpUWldCXG40TVpob3VsbFoxSUI0Wmg4VGpFOFJRQUNlMlhjeGRtem85bldBeDhYYy9NPVxuLS0tLS1FTkQgUlNBIFBSSVZBVEUgS0VZLS0tLS0ifQ==";

$auth = "rrykwkgojspeymijmdqfcykkuhkzukej";







$account = new Rubika($auth, $private_key);


// $account->on_message(function (Message $update) use ($account) {
//     if($update->is_group() && preg_match('/(@|rubika|http)/',$update->text()){
//         // پیام تبلیغاتی است
//         print("");
//     }
        
// });

$account = new Rubika($auth, $private_key);

// دریافت آپدیت های اکانت
$account->on_message(function (Message $update) use ($account) {
    $pattern = '';
    if ($update->is_group() && preg_match("/(@|rubika|http)/", $update->text())) {
        // پاک کردن پیام
        $account->deleteMessage($update->object_guid() , $update->message_id());
        //اخراج کاربر از گروه  
        $account->banChatMember($update->object_guid() , $update->author_guid());
    }


});

