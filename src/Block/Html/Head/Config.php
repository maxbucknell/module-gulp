<?php

namespace MaxBucknell\Prefab\Block\Html\Head;

use Magento\Framework\RequireJs\Config as RequireJsConfig;
use Magento\Framework\View\Asset\Minification;
use Magento\RequireJs\Block\Html\Head\Config as OriginalConfig;

class Config extends OriginalConfig
{
    private $fileManager;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        RequireJsConfig $config,
        \Magento\RequireJs\Model\FileManager $fileManager,
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magento\Framework\View\Asset\ConfigInterface $bundleConfig,
        Minification $minification,
        array $data = []
    ) {
        $this->fileManager = $fileManager;

        parent::__construct($context, $config, $fileManager, $pageConfig, $bundleConfig, $minification, $data);
    }


    protected function _prepareLayout()
    {
        $requireJsConfig = $this->fileManager->createRequireJsConfigAsset();
        $assetCollection = $this->pageConfig->getAssetCollection();

        $assetCollection->add(
            $requireJsConfig->getFilePath(),
            $requireJsConfig
        );
    }

}