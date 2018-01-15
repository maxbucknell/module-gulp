<?php

namespace MaxBucknell\Gulp\Model\DataProvider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\View\Design\Theme\FlyweightFactory;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\App\Emulation;
use MaxBucknell\Gulp\Api\DataProviderInterface;
use Magento\Theme\Model\View\Design;

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

    /**
     * @var Design
     */
    private $design;

    public function __construct(
        ComponentRegistrar $componentRegistrar,
        FlyweightFactory $flyweightFactory,
        ScopeConfigInterface $config,
        Design $design
    ) {
        $this->componentRegistrar = $componentRegistrar;
        $this->flyweightFactory = $flyweightFactory;
        $this->config = $config;
        $this->design = $design;
    }

    public function getData(StoreInterface $store)
    {
        $result = [];
        $themeId = $this->design->getConfigurationDesignTheme('frontend', [ 'store' => $store->getId()]);
        $theme = $this->flyweightFactory->create($themeId);
        $themes = $theme->getInheritedThemes();

        foreach ($themes as $theme) {
            $result[] = $this->componentRegistrar->getPath(ComponentRegistrar::THEME, $theme->getFullPath());
        }

        return \json_encode($result, JSON_FORCE_OBJECT);
    }
}
