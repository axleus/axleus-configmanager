<?php

declare(strict_types= 1);

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            "dependencies" => $this->getDependencies(),
            "listeners"=> $this->getListeners(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            "aliases"=> [],
            "deligators"=> [],
            'factories'=>[],
        ];
    }

    public function getListeners(): array
    {
        return [];    
    }
}