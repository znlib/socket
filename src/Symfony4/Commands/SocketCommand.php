<?php

namespace ZnLib\Socket\Symfony4\Commands;

use ZnBundle\Messenger\Domain\Libs\WordClassificator;
use Phpml\Classification\KNearestNeighbors;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Workerman\Worker;

class SocketCommand extends Command
{

    protected static $defaultName = 'socket:socket';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $worker = new Worker('websocket://0.0.0.0:8001');
        $worker->count = 4;
        $worker->onConnect = function ($connection) {
            $connection->send('message');
        };
        Worker::runAll();
    }
}
