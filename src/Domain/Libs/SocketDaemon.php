<?php

namespace ZnLib\Socket\Domain\Libs;

use ZnCore\Domain\Helpers\EntityHelper;
use ZnCore\Base\Exceptions\NotFoundException;
use ZnLib\Socket\Domain\Entities\SocketEventEntity;
use ZnLib\Socket\Domain\Enums\SocketEventEnum;
use ZnLib\Socket\Domain\Enums\SocketEventStatusEnum;
use ZnLib\Socket\Domain\Repositories\Ram\ConnectionRepository;
use Symfony\Component\Console\Application;
use ZnCore\Base\Libs\DotEnv\DotEnv;
use Illuminate\Container\Container;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Workerman\Connection\ConnectionInterface;
use Workerman\Worker;

class SocketDaemon {

    private $users = [];
    private $tcpWorker;
    private $wsWorker;
    private $localUrl = 'tcp://127.0.0.1:1234';
    private $connectionRepository;

    public function __construct(ConnectionRepository $connectionRepository)
    {
        $this->connectionRepository = $connectionRepository;
        // массив для связи соединения пользователя и необходимого нам параметра

        // создаём ws-сервер, к которому будут подключаться все наши пользователи
        $this->wsWorker = new Worker("websocket://0.0.0.0:8001");
        // создаём обработчик, который будет выполняться при запуске ws-сервера
        $this->wsWorker->onWorkerStart = [$this, 'onWsStart'];
        $this->wsWorker->onConnect = [$this, 'onWsConnect'];
        $this->wsWorker->onClose = [$this, 'onWsClose'];
    }

    public function sendMessageToTcp(SocketEventEntity $eventEntity) {
        // соединяемся с локальным tcp-сервером
        try {
            $instance = stream_socket_client($this->localUrl);
            $serialized = serialize($eventEntity);
            // отправляем сообщение
            fwrite($instance, $serialized . "\n");
        } catch (\Exception $e) {
            return false;
        }
    }

    public function onWsStart() {
        // создаём локальный tcp-сервер, чтобы отправлять на него сообщения из кода нашего сайта
        $this->tcpWorker = new Worker($this->localUrl);
        // создаём обработчик сообщений, который будет срабатывать,
        // когда на локальный tcp-сокет приходит сообщение
        $this->tcpWorker->onMessage = [$this, 'onTcpMessage'];
        $this->tcpWorker->listen();
    }

    public function onWsConnect(ConnectionInterface $connection) {
        $connection->onWebSocketConnect = function($connection)
        {
            $userId = intval($_GET['userId']);
            if(empty($userId)) {
                throw new Exception('Empty user id;');
            }
            // при подключении нового пользователя сохраняем get-параметр, который же сами и передали со страницы сайта
            $this->connectionRepository->addConnection($userId, $connection);
            // вместо get-параметра можно также использовать параметр из cookie, например $_COOKIE['PHPSESSID']

            $event = new SocketEventEntity;
            $event->setUserId($userId);
            $event->setName(SocketEventEnum::CONNECT);
            $event->setData([
                'totalConnections' => $this->connectionRepository->countByUserId($userId),
            ]);
            $this->sendToWebSocket($event, $connection);
        };
    }

    public function onWsClose(ConnectionInterface $connection) {
        $this->connectionRepository->remove($connection);
    }

    public function onTcpMessage(ConnectionInterface $connection, string $data) {
        /** @var SocketEventEntity $eventEntity */
        $eventEntity = unserialize($data);
        $userId = $eventEntity->getUserId();
        // отправляем сообщение пользователю по userId
        try {
            $webconnections = $this->connectionRepository->allByUserId($userId);
            foreach ($webconnections as $webconnection) {
                $this->sendToWebSocket($eventEntity, $webconnection);
            }
        } catch (NotFoundException $e) {}
    }

    public function runAll() {
        // Run worker
        Worker::runAll();
    }

    private function sendToWebSocket(SocketEventEntity $socketEventEntity, ConnectionInterface $connection) {
        $eventArray = EntityHelper::toArray($socketEventEntity);
        $json = json_encode($eventArray);
        $connection->send($json);
    }

}