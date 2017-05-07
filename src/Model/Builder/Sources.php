<?php

namespace MaxBucknell\Gulp\Model\Builder;

use Magento\Framework\Component\ComponentRegistrar;
use MaxBucknell\Gulp\Api\BuilderInterface;
use MaxBucknell\Gulp\Model\Filesystem;

class Sources implements BuilderInterface
{
    /**
     * @var ComponentRegistrar
     */
    private $componentRegistrar;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        ComponentRegistrar $componentRegistrar,
        Filesystem $filesystem
    ) {
        $this->componentRegistrar = $componentRegistrar;
        $this->filesystem = $filesystem;
    }

    public function build()
    {
        // Iterate through all modules
        $modules = $this->componentRegistrar->getPaths(ComponentRegistrar::MODULE);

        foreach ($modules as $name => $path) {
            $relativePath = $this->filesystem->getRelativeDirectory($path);
            $this->copyFilesForModule($relativePath, $name);
        }
    }

    /**
     * @param $path
     * @param $name
     */
    public function copyFilesForModule($path, $name)
    {
        $root = $this->filesystem->getLocation();
        $sourceDir = "{$path}/gulp";
        $destinationDir = "{$root}/{$name}";

        foreach ($this->filesystem->getFilesystem()->listContents($sourceDir, true) as $file) {
            $source = "{$sourceDir}/{$file['basename']}";
            $destination = "{$destinationDir}/{$file['basename']}";

            $this->filesystem->getFilesystem()->copy($source, $destination);
        }
    }
}