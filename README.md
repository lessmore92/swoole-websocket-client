
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
        $data = $webSocketClient->recv()->data;
        var_dump($data);
    }
});
```
