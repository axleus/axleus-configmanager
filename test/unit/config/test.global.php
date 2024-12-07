<?php

declare(strict_types=1);

use AxleusTest\ConfigManager\Resources\FooConfigProvider;

return [
    FooConfigProvider::class => [
        'test_key' => 'test_value',
    ],
];