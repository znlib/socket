<?php

namespace ZnLib\Socket;

use ZnCore\Base\Bundle\Base\BaseBundle;

class Bundle extends BaseBundle
{

    public function console(): array
    {
        return [
            'ZnLib\Socket\Symfony4\Commands',
        ];
    }
}
