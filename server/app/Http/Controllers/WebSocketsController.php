<?php

namespace App\Http\Controllers;

use App\Models\Coin;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class WebSocketsController extends Controller implements MessageComponentInterface {
    private $connections = [];

    public function onOpen(ConnectionInterface $connection) {
        $this->connections[$connection->resourceId] = ['connection' => $connection];
        echo 'Connected' . PHP_EOL;
    }

    public function onMessage(ConnectionInterface $connection, $message) {
        echo $message . PHP_EOL;
    }

    public function onClose(ConnectionInterface $connection) {
        echo 'Disconnected' . PHP_EOL;
        unset($this->connections[$connection->resourceId]);
    }

    public function onError(ConnectionInterface $connection, \Exception $error) {
        echo 'Websockets Server error: ' . $error->getMessage() . PHP_EOL;
        unset($this->connections[$connection->resourceId]);
        $connection->close();
    }
}
