<?php

namespace MaxBucknell\Gulp\Model;

use Magento\Store\Api\Data\StoreInterface;
use MaxBucknell\Gulp\Api\DataProviderInterface;

class DataProvider implements DataProviderInterface
{
    /**
     * @var array
     */
    private $dataProviders;

    public function __construct(
        $dataProviders = []
    ) {
        $this->dataProviders = $dataProviders;
    }

    public function getData(StoreInterface $store)
    {
        $data = [];
        foreach ($this->dataProviders as $key => $dataProvider) {
            /** @var DataProviderInterface $dataProvider */
            $data[$key] = $dataProvider->getData($store);
        }

        return $data;
    }

}