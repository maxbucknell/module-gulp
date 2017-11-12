<?php

namespace MaxBucknell\Prefab\Api;

use Magento\Store\Api\Data\StoreInterface;

interface DataProviderInterface
{
    public function getData(StoreInterface $store);
}