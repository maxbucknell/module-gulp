<?php

namespace MaxBucknell\Gulp\Model;


use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Magento\Framework\ObjectManagerInterface;

class FlysystemFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    public function create($root)
    {
        $adapter = $this->objectManager->create(Local::class, [ 'root' => $root ]);
        $filesystem = $this->objectManager->create(Filesystem::class, [ 'adapter' => $adapter ]);

        return $filesystem;
    }
}