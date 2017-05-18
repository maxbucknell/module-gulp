<?php

namespace MaxBucknell\Gulp\Model\DataProvider;


use Magento\Store\Api\Data\StoreInterface;
use MaxBucknell\Gulp\Api\DataProviderInterface;
use MaxBucknell\Gulp\Model\Filesystem;

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