<?php

namespace MaxBucknell\Prefab\Model\DataProvider;

use Magento\Store\Api\Data\StoreInterface;
use MaxBucknell\Prefab\Api\DataProviderInterface;

class StoreCode implements DataProviderInterface
{
    public function getData(StoreInterface $store)
    {
        return $store->getCode();
    }
}