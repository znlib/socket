<?php

namespace ZnLib\Socket\Domain\Libs;

use Workerman\Connection\ConnectionInterface;
use ZnDomain\Entity\Helpers\EntityHelper;
use ZnLib\Socket\Domain\Entities\SocketEventEntity;

class Transport
{

    private $localTcpUrl = 'tcp://127.0.0.1:1234';
    
    public function __construct($localTcpUrl)
    {
//        $this->localTcpUrl = $localTcpUrl;
    }

    public function sendMessageToTcp(SocketEventEntity $eventEntity)
    {
        // соединяемся с локальным tcp-сервером
        try {
            $instance = stream_socket_client($this->localTcpUrl);
            $serialized = serialize($eventEntity);
            // отправляем сообщение
            fwrite($instance, $serialized . "\n");
        } catch (\Exception $e) {
            return false;
        }
    }

    public function sendToWebSocket(SocketEventEntity $socketEventEntity, ConnectionInterface $connection)
    {
        $eventArray = EntityHelper::toArray($socketEventEntity);
        $json = json_encode($eventArray);
        $connection->send($json);
    }
}
