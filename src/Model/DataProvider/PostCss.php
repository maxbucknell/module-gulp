<?php

namespace MaxBucknell\Prefab\Model\DataProvider;


use Magento\Store\Api\Data\StoreInterface;
use MaxBucknell\Prefab\Api\DataProviderInterface;

class PostCss implements DataProviderInterface
{
    /**
     * @var array
     */
    private $plugins;

    public function __construct(
        $plugins = []
    ) {
        $this->plugins = $plugins;
    }

    public function getData(StoreInterface $store)
    {
        return \json_encode($this->plugins, JSON_FORCE_OBJECT);
    }

}