<?php

namespace ZnLib\Socket\Domain\Libs\Handlers;

use Workerman\Connection\ConnectionInterface;
use Workerman\Worker;
use ZnBundle\User\Domain\Interfaces\Services\AuthServiceInterface;
use ZnCore\Base\Exceptions\NotFoundException;
use ZnCore\Contract\User\Exceptions\UnauthorizedException;
use ZnCore\Contract\User\Interfaces\Entities\IdentityEntityInterface;
use ZnCore\Base\Libs\Entity\Helpers\EntityHelper;
use ZnLib\Socket\Domain\Entities\SocketEventEntity;
use ZnLib\Socket\Domain\Enums\SocketEventEnum;
use ZnLib\Socket\Domain\Libs\Transport;
use ZnLib\Socket\Domain\Repositories\Ram\ConnectionRepository;
use Workerman\Protocols\Http\Request;

class WsHandler
{

    private $tcpWorker;
    private $transport;
    private $wsWorker;
    private $localUrl = 'tcp://127.0.0.1:1234';
    private $connectionRepository;
    private $authService;

    public function __construct(
        Transport $transport,
        Worker $wsWorker, 
        Worker $tcpWorker,
        ConnectionRepository $connectionRepository, 
        AuthServiceInterface $authService
    )
    {
        $this->wsWorker = $wsWorker;
        $this->tcpWorker = $tcpWorker;
        $this->authService = $authService;
        $this->connectionRepository = $connectionRepository;
        $this->transport = $transport;
    }
    
    public function onWsStart()
    {
        $this->tcpWorker->listen();
    }

    public function onWsConnect(ConnectionInterface $connection)
    {
        $connection->onWebSocketConnect = function ($connection) {
            $userId = $this->auth($_GET);
            // при подключении нового пользователя сохраняем get-параметр, который же сами и передали со страницы сайта
            $this->connectionRepository->addConnection($userId, $connection);
            // вместо get-параметра можно также использовать параметр из cookie, например $_COOKIE['PHPSESSID']

            $event = new SocketEventEntity;
            $event->setUserId($userId);
            $event->setName(SocketEventEnum::CONNECT);
            $event->setData([
                'totalConnections' => $this->connectionRepository->countByUserId($userId),
            ]);
            $this->transport->sendToWebSocket($event, $connection);
        };
    }

    public function onWsClose(ConnectionInterface $connection)
    {
        $this->connectionRepository->remove($connection);
    }

    public function onWsMessage(ConnectionInterface $connection,  $jsonMessage)
    {
        $data = json_decode($jsonMessage, JSON_OBJECT_AS_ARRAY);
        $event = new SocketEventEntity;
        $event->setUserId($data['toAddress']);
        $event->setName('cryptoMessage.p2p');
        $event->setData([
            'document' => $data['document'],
        ]);
        $this->transport->sendMessageToTcp($event);
    }

    protected function auth($params)
    {
        $token = $params['token'] ?? null;
        if (!empty($token)) {
            /** @var IdentityEntityInterface $identityEntity */
            $identityEntity = $this->authService->authenticationByToken($token);
            return $identityEntity->getId();
        }
        throw new UnauthorizedException('Empty user id');
    }

}
