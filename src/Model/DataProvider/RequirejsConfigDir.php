<?php

namespace MaxBucknell\Gulp\Model\DataProvider;


use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Design\Theme\FlyweightFactory;
use Magento\Store\Api\Data\StoreInterface;
use MaxBucknell\Gulp\Api\DataProviderInterface;
use MaxBucknell\Gulp\Model\Filesystem;

class RequirejsConfigDir implements DataProviderInterface
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

    public function __construct(
        Filesystem $filesystem,
        FlyweightFactory $flyweightFactory,
        ScopeConfigInterface $config
    ) {
        $this->filesystem = $filesystem;
        $this->flyweightFactory = $flyweightFactory;
        $this->config = $config;
    }

    public function getData(StoreInterface $store)
    {
        $root = $this->filesystem->getRootDirectory();
        $locale = $this->config->getValue('general/locale/code', 'stores', $store->getId());
        $themeId = $this->config->getValue('design/theme/theme_id', 'stores', $store->getId());
        $theme = $this->flyweightFactory->create($themeId);

        $themePath = $theme->getFullPath();

        return "{$root}/pub/static/_requirejs/{$themePath}/{$locale}";
    }


}