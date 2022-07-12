<?php

namespace ZnLib\Socket\Domain\Apps;

use ZnCore\Arr\Helpers\ArrayHelper;
use ZnCore\DotEnv\Domain\Libs\DotEnv;
use ZnLib\Socket\Domain\Apps\Base\BaseWebSocketApp;

class WebSocketApp extends BaseWebSocketApp
{

    protected function bundles(): array
    {
        $bundles = [
            \ZnDatabase\Base\Bundle::class,
        ];
        if (DotEnv::get('BUNDLES_CONFIG_FILE')) {
            $bundles = ArrayHelper::merge($bundles, include DotEnv::get('BUNDLES_CONFIG_FILE'));
        }
        return $bundles;
    }
}
