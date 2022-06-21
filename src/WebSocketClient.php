<?php
/**
 * User: Lessmore92
 * Date: 6/21/2022
 * Time: 4:47 AM
 */

namespace Lessmore92\Swoole;

use Lessmore92\Psr\Exceptions\InvalidUrl;
use Lessmore92\Psr\Uri;
use Lessmore92\Swoole\Exceptions\WebSocketException;
use Psr\Http\Message\UriInterface;
use Swoole\Coroutine\Http\Client;
use Swoole\Http\Status;
use Swoole\WebSocket\Frame;

class WebSocketClient
{
    public Client $client;
    /**
     * @var UriInterface
     */
    private $uri;


    /**
     * @param Uri|string $uri
     * @throws WebSocketException|InvalidUrl
     */
    public function __construct($uri)
    {
        if (is_string($uri))
        {
            $this->uri = new Uri($uri);
        }
        else if (class_implements($uri, UriInterface::class))
        {
            $this->uri = $uri;
        }
        else
        {
            throw new InvalidUrl('Url must be a string or implemented UriInterface.');
        }

        $host = $this->uri->getHost();
        $port = $this->uri->getPort();
        $ssl  = $this->uri->getScheme() === 'wss';
        if (empty($host))
        {
            throw new InvalidUrl('The WebSocket host should not be empty.');
        }

        if (empty($port))
        {
            $port = $ssl ? 443 : 80;
        }

        $this->client = new Client($host, $port, $ssl);

        parse_str($this->uri->getQuery(), $query);
        $query = http_build_query($query);

        $path = $this->uri->getPath() ?: '/';
        $path = empty($query) ? $path : $path . '?' . $query;

        $ret = $this->client->upgrade($path);
        if (!$ret)
        {
            if ($this->client->errCode !== 0)
            {
                $errCode = $this->client->errCode;
                $errMsg  = $this->client->errMsg;
            }
            else
            {
                $errCode = $this->client->statusCode;
                $errMsg  = Status::getReasonPhrase($errCode);
            }

            throw new WebSocketException('Websocket upgrade failed by [' . $errMsg . '(' . $errCode . ')' . '].', $errCode);
        }
    }

    /**
     * @param float|int $timeout
     * @return Frame
     */
    public function recv(float $timeout = -1)
    {
        return $this->client->recv($timeout);
    }

    public function push(string $data, int $opcode = WEBSOCKET_OPCODE_TEXT, bool $finish = true): bool
    {
        return $this->client->push($data, $opcode, $finish);
    }

    public function close(): bool
    {
        return $this->client->close();
    }

    public function __destruct()
    {
        $this->close();
    }
}
