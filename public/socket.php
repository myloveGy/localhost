<?php

use Swoole\WebSocket\Server;

$ws = new Server('0.0.0.0', 9502);

//监听WebSocket连接打开事件
$ws->on('Open', function ($ws, $request) {
    $ws->push($request->fd, "hello, welcome\n" . $request->fd);
});

// 监听WebSocket消息事件
$ws->on('Message', function ($ws, $frame) {
    echo "Message: $frame->data\n";
    $ws->push($frame->fd, "server: $frame->data");
});

// 监听WebSocket连接关闭事件
$ws->on('Close', function ($ws, $fd) {
    echo "client-$fd is closed\n";
});

$ws->start();
