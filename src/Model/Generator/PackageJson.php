<?php

namespace MaxBucknell\Gulp\Model\Generator;

class PackageJson
{
    public function generate($config = [])
    {
        $result = [
            'name' => '@maxbucknell/magento-gulp',
            'version' => '1.0.0',
            'description' => '',
            'main' => 'gulpfile.js',
            'author' => '',
            'license' => 'MIT',
            'dependencies' => $config
        ];

        return \json_encode($result, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT | JSON_UNESCAPED_SLASHES);
    }
}