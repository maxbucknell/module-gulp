<?php

namespace MaxBucknell\Gulp\Model\DataProvider;


use Magento\Store\Api\Data\StoreInterface;
use MaxBucknell\Gulp\Api\DataProviderInterface;

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
        return $this->plugins;
    }

}