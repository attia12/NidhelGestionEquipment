<?php
namespace App\Service;

use Ratchet\Client\Connector;
use Ratchet\Client\WebSocket;
use React\EventLoop\Factory as LoopFactory;
use Psr\Http\Message\ResponseInterface;
use React\Promise\PromiseInterface;
use Exception;
class WebSocketNotificationService
{
    public function notifyClients(string $message)
    {
        $loop = LoopFactory::create();
        $connector = new Connector($loop);

        $connector('ws://127.0.0.1:8080')
            ->then(
                function (WebSocket $conn) use ($message) {
                    $conn->send($message);
                    $conn->close();
                },
                function (Exception $e) {
                    echo "Could not connect: {$e->getMessage()}\n";
                }
            );

        $loop->run();
    }
}