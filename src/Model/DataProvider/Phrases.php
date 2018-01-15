<?php

namespace MaxBucknell\Prefab\Model\DataProvider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Translate;
use Magento\Framework\TranslateFactory;
use Magento\Framework\View\DesignInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\App\Emulation;
use MaxBucknell\Prefab\Api\DataProviderInterface;
use Magento\Theme\Model\View\Design;

class Phrases implements DataProviderInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * @var Emulation
     */
    private $emulation;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Design
     */
    private $design;

    public function __construct(
        ScopeConfigInterface $config,
        Emulation $emulation,
        ObjectManagerInterface $objectManager,
        Design $design
    ) {
        $this->config = $config;
        $this->emulation = $emulation;
        $this->objectManager = $objectManager;
        $this->design = $design;
    }

    public function getData(StoreInterface $store)
    {
        $themeId = $this->design->getConfigurationDesignTheme('frontend', [ 'store' => $store->getId()]);
        $locale = $this->config->getValue('general/locale/code', 'stores', $store->getId());

        /** @var DesignInterface $viewDesign */
        $viewDesign = $this->objectManager->create(DesignInterface::class);
        $viewDesign->setDesignTheme($themeId, 'frontend');

        /** @var Translate $translate */
        $translate = $this->objectManager->create(
            Translate::class,
            [
                'viewDesign' => $viewDesign,
            ]
        );

        $translate->setLocale($locale);
        $translate->loadData();

        return \json_encode($translate->getData(), JSON_FORCE_OBJECT);
    }
}
