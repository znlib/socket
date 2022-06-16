<?php

namespace ZnLib\Socket\Domain\Apps\Base;

use Psr\Container\ContainerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use ZnCore\Base\Libs\App\Interfaces\ConfigManagerInterface;
use ZnCore\Base\Libs\Container\Interfaces\ContainerConfiguratorInterface;
use ZnLib\Socket\Domain\Libs\SocketDaemon;
use ZnSandbox\Sandbox\App\Base\BaseApp;
use ZnSandbox\Sandbox\App\Libs\ZnCore;
use ZnSandbox\Sandbox\App\Subscribers\ConsoleDetectTestEnvSubscriber;

abstract class BaseWebSocketApp extends BaseApp
{

    private $configManager;

    public function __construct(
        ContainerInterface $container,
        EventDispatcherInterface $dispatcher,
        ZnCore $znCore,
        ContainerConfiguratorInterface $containerConfigurator,
        ConfigManagerInterface $configManager
    )
    {
        parent::__construct($container, $dispatcher, $znCore, $containerConfigurator);
        $this->configManager = $configManager;
    }

    public function appName(): string
    {
        return 'webSocket';
    }

    public function subscribes(): array
    {
        return [
            ConsoleDetectTestEnvSubscriber::class,
        ];
    }

    /*public function subscribes(): array
    {
        return [
            ConsoleDetectTestEnvSubscriber::class,
        ];
    }*/

    public function import(): array
    {
        return ['i18next', 'container', 'console', 'migration', 'rbac', 'symfonyRpc', 'telegramRoutes'];
    }

    public function init(): void
    {
        parent::init();
        $consoleCommands = $this->configManager->get('consoleCommands');
//        $this->createConsole($consoleCommands);
    }

    protected function configContainer(ContainerConfiguratorInterface $containerConfigurator): void
    {
        $containerConfigurator->singleton(SocketDaemon::class, SocketDaemon::class);
    }

//    protected function createConsole(array $consoleCommands)
//    {
//        $container = $this->getContainer();
//
//        /** @var Application $application */
//        $application = $container->get(Application::class);
//        $application->getDefinition()->addOptions([
//            new InputOption(
//                '--env',
//                '-e',
//                InputOption::VALUE_OPTIONAL,
//                'The environment to operate in.',
//                'DEV'
//            )
//        ]);
//        if (!empty($consoleCommands)) {
//            CommandHelper::registerFromNamespaceList($consoleCommands, $container, $application);
//        }
//    }
}
