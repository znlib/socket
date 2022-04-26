<?php

namespace ZnLib\Socket\Domain\Apps;

use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Base\Libs\DotEnv\DotEnv;
use ZnLib\Socket\Domain\Apps\Base\BaseWebSocketApp;

class WebSocketApp extends BaseWebSocketApp
{

    protected function bundles(): array
    {
        $bundles = [
            \ZnDatabase\Base\Bundle::class,
        ];
        if (DotEnv::get('BUNDLES_CONFIG_FILE')) {
            $bundles = ArrayHelper::merge($bundles, include __DIR__ . '/../../../../../../' . DotEnv::get('BUNDLES_CONFIG_FILE'));
        }
        return $bundles;
    }
}
