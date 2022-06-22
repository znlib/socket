<?php

namespace ZnLib\Socket\Symfony4\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZnLib\Socket\Domain\Libs\SocketDaemon;

class SocketCommand extends Command
{

    protected static $defaultName = 'socket:worker';
    private $socketDaemon;

    public function __construct($name = null, SocketDaemon $socketDaemon)
    {
        parent::__construct($name);
        $this->socketDaemon = $socketDaemon;
    }

    protected function configure()
    {
        $this->addArgument('workerCommand', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $argv;

        $argv[1] = $input->getArgument('workerCommand');

        /*if($input->getArgument('workerCommand') == 'start') {
            $argv[1] = 'start';
        } elseif ($input->getArgument('workerCommand') == 'connections') {
            $argv[1] = 'connections';
        } elseif ($input->getArgument('workerCommand') == 'status') {
            $argv[1] = 'status';
        }*/

        $this->socketDaemon->runAll();

        return Command::SUCCESS;
    }
}
