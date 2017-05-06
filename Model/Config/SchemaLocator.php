<?php

namespace MaxBucknell\Gulp\Model\Config;


use Magento\Framework\Config\SchemaLocatorInterface;
use Magento\Framework\Module\Dir\Reader as ModuleDirReader;

class SchemaLocator implements SchemaLocatorInterface
{
    const CONFIG_FILE_SCHEMA = 'gulp.xsd';

    private $schema;

    public function __construct(
        ModuleDirReader $moduleReader
    ) {
        $configDir = $moduleReader->getModuleDir(Dir::MODULE_ETC_DIR, 'MaxBucknell_Gulp');

        $this->schema = $configDir . DIRECTORY_SEPARATOR . self::CONFIG_FILE_SCHEMA;
    }

    public function getSchema()
    {
        return $this->schema;
    }

    public function getPerFileSchema()
    {
        return $this->schema;
    }
}