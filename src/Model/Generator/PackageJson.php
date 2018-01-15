<?php

namespace MaxBucknell\Prefab\Model\Generator;

class PackageJson
{
    public function generate($config = [])
    {
        $result = [
            'name' => '@maxbucknell/magento-prefab',
            'version' => '1.0.0',
            'description' => '',
            'author' => '',
            'license' => 'MIT',
            'dependencies' => $config['dependencies'],
            'scripts' => $config['scripts']
        ];

        return \json_encode($result, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT | JSON_UNESCAPED_SLASHES);
    }
}