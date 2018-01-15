<?php

namespace MaxBucknell\Prefab\Model\DataProvider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\Data\StoreInterface;
use MaxBucknell\Prefab\Api\DataProviderInterface;

class Config implements DataProviderInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * @var array
     */
    private $configFields;

    public function __construct(
        ScopeConfigInterface $config,
        $configFields = []
    ) {
        $this->config = $config;
        $this->configFields = $configFields;
    }

    public function getData(StoreInterface $store)
    {
        $result = [];
        foreach ($this->configFields as $configField) {
            $result[$configField] = $this->config->getValue($configField, 'stores', $store->getId());
        }

        return \json_encode($result, JSON_FORCE_OBJECT);
    }

}