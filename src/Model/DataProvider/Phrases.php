<?php

namespace MaxBucknell\Gulp\Model\DataProvider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Translate;
use Magento\Framework\TranslateFactory;
use Magento\Framework\View\DesignInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\App\Emulation;
use MaxBucknell\Gulp\Api\DataProviderInterface;

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

    public function __construct(
        ScopeConfigInterface $config,
        Emulation $emulation,
        ObjectManagerInterface $objectManager
    ) {
        $this->config = $config;
        $this->emulation = $emulation;
        $this->objectManager = $objectManager;
    }

    public function getData(StoreInterface $store)
    {
        $theme = $this->config->getValue('design/theme/theme_id', 'stores', $store->getId());
        $locale = $this->config->getValue('general/locale/code', 'stores', $store->getId());

        /** @var DesignInterface $viewDesign */
        $viewDesign = $this->objectManager->create(DesignInterface::class);
        $viewDesign->setDesignTheme($theme, 'frontend');

        /** @var Translate $translate */
        $translate = $this->objectManager->create(
            Translate::class,
            [
                'viewDesign' => $viewDesign,
            ]
        );

        $translate->setLocale($locale);
        $translate->loadData();

        return $translate->getData();
    }
}