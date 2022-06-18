<?php

namespace ZnLib\Socket\Domain\Apps\Base;

use Psr\Container\ContainerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use ZnCore\Base\Libs\App\Interfaces\ConfigManagerInterface;
use ZnCore\Base\Libs\Container\Interfaces\ContainerConfiguratorInterface;
use ZnLib\Socket\Domain\Libs\SocketDaemon;
use ZnCore\Base\Libs\App\Base\BaseApp;
use ZnSandbox\Sandbox\App\Libs\ZnCore;
use ZnLib\Console\Domain\Subscribers\ConsoleDetectTestEnvSubscriber;

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

    public function import(): array
    {
        return ['i18next', 'container', 'console', 'migration', 'rbac', 'symfonyRpc', 'telegramRoutes'];
    }

    protected function configContainer(ContainerConfiguratorInterface $containerConfigurator): void
    {
        $containerConfigurator->singleton(SocketDaemon::class, SocketDaemon::class);
    }
}
