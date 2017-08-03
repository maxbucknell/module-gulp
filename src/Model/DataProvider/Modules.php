<?php

namespace MaxBucknell\Gulp\Model\DataProvider;


use Magento\Framework\Component\ComponentRegistrar;
use Magento\Store\Api\Data\StoreInterface;
use MaxBucknell\Gulp\Api\DataProviderInterface;

class Modules implements DataProviderInterface
{
    /**
     * @var ComponentRegistrar
     */
    private $componentRegistrar;

    public function __construct(
        ComponentRegistrar $componentRegistrar
    ) {
        $this->componentRegistrar = $componentRegistrar;
    }

    public function getData(StoreInterface $store)
    {
        return \json_encode($this->componentRegistrar->getPaths(ComponentRegistrar::MODULE), JSON_FORCE_OBJECT);
    }
}