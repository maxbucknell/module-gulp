<?php

namespace MaxBucknell\Prefab\Model\DataProvider;


use Magento\Store\Api\Data\StoreInterface;
use MaxBucknell\Prefab\Api\DataProviderInterface;
use MaxBucknell\Prefab\Model\Filesystem;

class BuildDir implements DataProviderInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        Filesystem $filesystem
    ) {
        $this->filesystem = $filesystem;
    }

    public function getData(StoreInterface $store)
    {
        $root = $this->filesystem->getRootDirectory();
        $storeCode = $store->getCode();

        return "{$root}/pub/static/prefab_build/{$storeCode}";
    }


}