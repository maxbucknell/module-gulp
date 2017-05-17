<?php

namespace MaxBucknell\Gulp\Model\DataProvider;

use Magento\Store\Api\Data\StoreInterface;
use MaxBucknell\Gulp\Api\DataProviderInterface;

class StoreCode implements DataProviderInterface
{
    public function getData(StoreInterface $store)
    {
        return $store->getCode();
    }
}