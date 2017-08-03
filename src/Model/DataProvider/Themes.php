<?php

namespace MaxBucknell\Gulp\Model\DataProvider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\View\Design\Theme\FlyweightFactory;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\App\Emulation;
use MaxBucknell\Gulp\Api\DataProviderInterface;

class Themes implements DataProviderInterface
{
    /**
     * @var ComponentRegistrar
     */
    private $componentRegistrar;

    /**
     * @var FlyweightFactory
     */
    private $flyweightFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    public function __construct(
        ComponentRegistrar $componentRegistrar,
        FlyweightFactory $flyweightFactory,
        ScopeConfigInterface $config
    ) {
        $this->componentRegistrar = $componentRegistrar;
        $this->flyweightFactory = $flyweightFactory;
        $this->config = $config;
    }

    public function getData(StoreInterface $store)
    {
        $result = [];
        $themeId = $this->config->getValue('design/theme/theme_id', 'stores', $store->getId());
        $theme = $this->flyweightFactory->create($themeId);
        $themes = $theme->getInheritedThemes();

        foreach ($themes as $theme) {
            $result[] = $this->componentRegistrar->getPath(ComponentRegistrar::THEME, $theme->getFullPath());
        }

        return \json_encode($result, JSON_FORCE_OBJECT);
    }
}