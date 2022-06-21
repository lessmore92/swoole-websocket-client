# Easy-to-use Swoole WebSocket Client

## Installation

```
composer require lessmore92/swoole-websocket-client
```

## Requirements

* PHP>=7.4
* [openswoole](https://openswoole.com/docs/get-started/installation)-4.11.1 (not tested other versions, maybe it works.)

## Usage

Just pass the web socket URL to `WebSocketClient`.


### Note:
* This library is for CLI-based scripts and does not work for web-based ones.
* Because of web socket is async (and maybe be an always running task) it needs to be executed in the Swoole coroutine (`Co\run`); see sample below.

## Sample

```
use Lessmore92\Swoole\WebSocketClient;
use function Co\run;

require_once "vendor/autoload.php";

run(function () {
    $webSocketClient = new WebSocketClient('wss://socket.MyFancyApp.io:2053/app/app-key?protocol=7&client=js&version=7.0.6&flash=false');
    $webSocketClient->push('{"event":"pusher:subscribe","data":{"auth":"","channel":"msgs"}}');
    $webSocketClient->recv();

    while ($webSocketClient->client->connected)
    {
        $data = $webSocketClient->recv();
        var_dump($data);
    }
});
```
