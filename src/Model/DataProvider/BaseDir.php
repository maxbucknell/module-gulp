<?php

namespace MaxBucknell\Prefab\Model\DataProvider;


use Magento\Store\Api\Data\StoreInterface;
use MaxBucknell\Prefab\Api\DataProviderInterface;
use MaxBucknell\Prefab\Model\Filesystem;

class BaseDir implements DataProviderInterface
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
        return $this->filesystem->getRootDirectory();
    }
}