<?php

namespace MaxBucknell\Gulp\Model\DataProvider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Design\Theme\FlyweightFactory;
use Magento\Store\Api\Data\StoreInterface;
use MaxBucknell\Gulp\Api\DataProviderInterface;
use MaxBucknell\Gulp\Model\Filesystem;
use Magento\Theme\Model\View\Design;

class OutputDir implements DataProviderInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

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
        Filesystem $filesystem,
        FlyweightFactory $flyweightFactory,
        ScopeConfigInterface $config,
        Design $design
    ) {
        $this->filesystem = $filesystem;
        $this->flyweightFactory = $flyweightFactory;
        $this->config = $config;
        $this->design = $design;
    }

    public function getData(StoreInterface $store)
    {
        $root = $this->filesystem->getRootDirectory();
        $locale = $this->config->getValue('general/locale/code', 'stores', $store->getId());
        $themeId = $this->design->getConfigurationDesignTheme('frontend', [ 'store' => $store->getId()]);
        $theme = $this->flyweightFactory->create($themeId);

        $themePath = $theme->getFullPath();

        return "{$root}/pub/static/{$themePath}/{$locale}";
    }
}
