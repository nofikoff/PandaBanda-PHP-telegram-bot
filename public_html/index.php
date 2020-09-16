<?php
/* заметки

@ChernivtsiTheBest_bot
@ChernivtsiTheBest_bot

//https://pogonyalo.com/test/chernovtsy/?order=1

t.me/delivery_pandaban_bot

https://api.telegram.org/bot1311343166:AAGWdjwE2kVkfTC-5zfE--v_etTFo0kapJY/setwebhook?url=https://pogonyalo.com/test/chernovtsy/
{
  "ok": true,
  "result": true,
  "description": "Webhook was set"
}
*/


require __DIR__ . "/PandaBandaBot.php";

$params = new stdClass();
$params->APIKey = '1311343166:AAGWdjwE2kVkfTC-5zfE--v_etTFo0kapJY';
$params->adminIDChat = ['440046277'];
$params->menegerOrdersIDChat = ['440046277', '440046277'];
$params->message2courier = "test 🚗";
$pp = new PandaBandaBot($params);




