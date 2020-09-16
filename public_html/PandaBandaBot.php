<?php

class PandaBandaBot
{
    public $params, $telegramRequest;


    public function __construct($params)
    {
        $this->params = $params;
        $this->telegramRequest = @json_decode(file_get_contents("php://input"));
        $this->request_incoming();

    }

    // Контролер вншених запросов
    // СОБЫТИЯ В СИСТЕМЕ
    // 1. Снаружи приходит запрос (GET POST) от третьего лица - генерируем сообщение типа ВОТ ТВОЯ ДОСТАВКА
    // 2. Пользователь нажимает кнопку в какомто сообщени
    // 3. Пользовател что то нам пишет HELLO

    public function request_incoming()
    {
        // все другие пост гет запросы
        if (isset($_REQUEST['order'])) {
            $this->logs("poster.txt", print_r($_REQUEST, TRUE));
            // ОТПРАВОЛЯЕМ ЗАКАЗ КУРЬЕРУ
            // ОТПРАВОЛЯЕМ ЗАКАЗ КУРЬЕРУ
            $this->parseRequest001createMessage();
            // от сервера ТЛЕГРАМ
        } else if ($this->telegramRequest) {
            $this->logs("telegramIncome.txt", print_r($this->telegramRequest, TRUE));
            // если ппользоватьль набрал /start

            if ($this->telegramRequest->message->text === "/start") {
                $this->telegramSend(
                    $this->telegramRequest->message->chat->id,
                    "Сообщите менеджеру для регистрации в системе ваш чат телеграм ID : \n" . $this->telegramRequest->message->chat->id);
            } elseif (isset($this->telegramRequest->message->text)) {
                $this->telegramSend(
                    $this->telegramRequest->message->chat->id,
                    "Не понял"
                );
            }
            //
            // если ппользоватьль нажал кнопку ДОСТАВЛЕНО
            elseif (isset($this->telegramRequest->callback_query)) $this->controllerStartButoon();
        }


    }

    private function controllerStartButoon()
    {

        //исзменить кнопку у курьера
        $this->telegramSend(
            $this->telegramRequest->callback_query->from->id,
            $this->telegramRequest->callback_query->message->text .
            "\n\n Доставлено " . date("H:i") 
            ,
            [], //нет кнопкопок
            'editMessageText',
            $this->telegramRequest->callback_query->message->message_id

        );


        // информируес менеджеров
        $this->telegramSend(
            $this->params->menegerOrdersIDChat,
            "Выполнен в " . date("H:i")
        );


    }

    private function parseRequest001createMessage()
    {

        $message = "Вот твой заказ для доставки КУРЬЕР";

        // Шлем сообщение
        // Шлем сообщение
        $to = $this->params->adminIDChat;
        $button =
            [
                [['text' => 'ДОСТАВИЛ ⏱ ' . date('H:i:s', time()) . '', 'callback_data' => '/?button=1']]
            ];
        $this->telegramSend($to, $message, $button);
        $this->logs("myRequest.txt", print_r([$to, $message], TRUE));


    }


    function telegramSend($telegramchatid, $msg, $button = [], $apiMethod = 'sendMessage', $editMessageID = '')
    {
        $this->logs("myRequest.txt", print_r([$this->telegramRequest->message->chat->id, $msg], TRUE));

        $url = 'https://api.telegram.org/bot' . $this->params->APIKey . '/' . $apiMethod;
        //
        $list = [];
        // если не массив
        if (!is_array($telegramchatid)) $list[] = $telegramchatid;
        else $list = $telegramchatid;


        foreach ($list as $chatId) {
            $data = [
                'chat_id' => $chatId,
                'text' => $msg,
                'parse_mode' => 'html',
//                'parse_mode' => 'markdown',
                'disable_web_page_preview' => 1,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $button
                ]),
                'message_id' => $editMessageID
            ];

            $options = ['http' =>
                [
                    'method' => 'POST',
                    'header' => "Content-Type:application/x-www-form-urlencoded\r\n",
                    'content' => http_build_query($data),
                ],
            ];
            $context = stream_context_create($options);
            $responce = file_get_contents($url, false, $context);
            //
            $this->logs("sendmessagetotelgramServer.txt", print_r($data, true));
            $this->logs("telegramResponseOnMyRequest.txt", $responce);
        }

        echo('{"message":"Успешно отправлено"}');

    }

    private function logs($filelog_name, $message)
    {
        $fd = fopen(__DIR__ . "/logs/" . $filelog_name, "a");
        fwrite($fd, date("Ymd-G:i:s")
            . " -------------------------------- \n\n" . $message . "\n\n");
        fclose($fd);
    }

}
