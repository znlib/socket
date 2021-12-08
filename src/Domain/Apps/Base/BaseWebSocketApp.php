<?php

namespace ZnLib\Socket\Domain\Apps\Base;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use ZnCore\Base\Libs\App\Helpers\ContainerHelper;
use ZnCore\Base\Libs\App\Interfaces\ConfigManagerInterface;
use ZnCore\Base\Libs\App\Interfaces\ContainerConfiguratorInterface;
use ZnCore\Contract\Kernel\Interfaces\KernelInterface;
use ZnLib\Console\Symfony4\Helpers\CommandHelper;
use ZnLib\Rpc\Symfony4\HttpKernel\RpcFramework;
use ZnLib\Socket\Domain\Libs\SocketDaemon;
use ZnSandbox\Sandbox\App\Base\BaseApp;
use ZnSandbox\Sandbox\App\Libs\ZnCore;
use ZnSandbox\Sandbox\App\Subscribers\ConsoleDetectTestEnvSubscriber;
use ZnSandbox\Sandbox\App\Subscribers\WebDetectTestEnvSubscriber;
use ZnSandbox\Sandbox\Rpc\Domain\Subscribers\ApplicationAuthenticationSubscriber;
use ZnSandbox\Sandbox\Rpc\Domain\Subscribers\CheckAccessSubscriber;
use ZnSandbox\Sandbox\Rpc\Domain\Subscribers\CryptoProviderSubscriber;
use ZnSandbox\Sandbox\Rpc\Domain\Subscribers\LanguageSubscriber;
use ZnSandbox\Sandbox\Rpc\Domain\Subscribers\LogSubscriber;
use ZnSandbox\Sandbox\Rpc\Domain\Subscribers\RpcFirewallSubscriber;
use ZnSandbox\Sandbox\Rpc\Domain\Subscribers\TimestampSubscriber;
use ZnSandbox\Sandbox\Rpc\Domain\Subscribers\UserAuthenticationSubscriber;

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

    /*public function subscribes(): array
    {
        return [
            ConsoleDetectTestEnvSubscriber::class,
        ];
    }*/

    public function import(): array
    {
        return ['i18next', 'container', 'console', 'migration', 'symfonyRpc', 'telegramRoutes'];
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

    protected function configDispatcher(EventDispatcherInterface $dispatcher): void
    {

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
