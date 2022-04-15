<?php

namespace ZnLib\Socket\Domain\Libs\Handlers;

use Workerman\Connection\ConnectionInterface;
use Workerman\Worker;
use ZnBundle\User\Domain\Interfaces\Services\AuthServiceInterface;
use ZnCore\Base\Enums\Measure\ByteEnum;
use ZnCore\Base\Exceptions\NotFoundException;
use ZnBundle\User\Domain\Exceptions\UnauthorizedException;
use ZnCore\Base\Legacy\Yii\Helpers\FileHelper;
use ZnCore\Contract\User\Interfaces\Entities\IdentityEntityInterface;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnLib\Socket\Domain\Entities\SocketEventEntity;
use ZnLib\Socket\Domain\Enums\SocketEventEnum;
use ZnLib\Socket\Domain\Libs\Transport;
use ZnLib\Socket\Domain\Repositories\Ram\ConnectionRepository;
use Workerman\Protocols\Http\Request;

class TcpHandler
{

    private $transport;
    private $tcpWorker;
    private $wsWorker;
    private $connectionRepository;
    private $authService;

    public function __construct(
        Transport $transport,
        Worker $wsWorker,
        Worker $tcpWorker,
        ConnectionRepository $connectionRepository
//        AuthServiceInterface $authService
    )
    {
        $this->wsWorker = $wsWorker;
        $this->tcpWorker = $tcpWorker;
        $this->connectionRepository = $connectionRepository;
        $this->transport = $transport;
    }

    public function onTcpMessage(ConnectionInterface $connection, string $data)
    {
        /** @var SocketEventEntity $eventEntity */
        $eventEntity = unserialize($data);
        $userId = $eventEntity->getUserId();
        // отправляем сообщение пользователю по userId
        try {
            $webconnections = $this->connectionRepository->allByUserId($userId);
            foreach ($webconnections as $webconnection) {
                $this->transport->sendToWebSocket($eventEntity, $webconnection);
                echo 
                    'send '.hash('crc32b', $data).
                    ' to ' . $userId . 
//                    ' ' . FileHelper::sizeFormat(mb_strlen(json_encode($eventEntity->getData()), '8bit')) .
                    PHP_EOL;
            }
        } catch (NotFoundException $e) {
        }
    }
}
