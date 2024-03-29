<?php

include '../vendor/autoload.php';

$config = include '../config/main.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

// 创建连接
$connection = new AMQPStreamConnection(
    getValue($config, 'rabbitmq.host', 'localhost'),
    getValue($config, 'rabbitmq.port', 5672),
    getValue($config, 'rabbitmq.username', 'guest'),
    getValue($config, 'rabbitmq.password', ''),
    getValue($config, 'rabbitmq.virtual-host', '/')
);

// 渠道
$channel = $connection->channel();

echo " [*] Waiting for logs. To exit press CTRL+C\n";

$channel->basic_consume('spring_queue', '', false, false, false, false, function ($message) {
    /* @var $message \PhpAmqpLib\Message\AMQPMessage */
    var_dump($message->getBody());

    if ($message->getDeliveryTag() == 1) {
        // 回复消息
        $message->getChannel()->basic_ack($message->getDeliveryTag(), false);
    }
});

// 一直监听消息
while ($channel->is_open()) {
    $channel->wait();
}

// 关闭链接
$channel->close();
$connection->close();
