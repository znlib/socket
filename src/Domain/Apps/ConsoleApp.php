<?php

namespace ZnLib\Socket\Domain\Apps;

use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Base\Libs\DotEnv\DotEnv;
use ZnLib\Console\Symfony4\Base\BaseConsoleApp;

class ConsoleApp extends BaseConsoleApp
{

    protected function bundles(): array
    {
        $bundles = [
//            new \ZnCore\Base\Libs\App\Bundle(['all']),
//            new \App\AppConsole\Bundle(['all']),

//            \ZnTool\Package\Bundle::class,
            \ZnLib\Db\Bundle::class,
//            \ZnLib\Fixture\Bundle::class,
//            \ZnLib\Migration\Bundle::class,
//            \ZnTool\Generator\Bundle::class,
//            \ZnTool\Stress\Bundle::class,
//            \ZnBundle\Queue\Bundle::class,
            //\ZnBundle\User\NewBundle::class,
        ];
        if (DotEnv::get('BUNDLES_CONFIG_FILE')) {
            $bundles = ArrayHelper::merge($bundles, include __DIR__ . '/../../../../../../' . DotEnv::get('BUNDLES_CONFIG_FILE'));
        }
        return $bundles;
    }
}
