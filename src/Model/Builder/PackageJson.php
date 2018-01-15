<?php

namespace MaxBucknell\Prefab\Model\Builder;

use MaxBucknell\Prefab\Api\BuilderInterface;
use MaxBucknell\Prefab\Model\Generator\PackageJson as PackageJsonGenerator;
use MaxBucknell\Prefab\Model\Filesystem;
use MaxBucknell\Prefab\Model\Config\Data as PrefabConfig;

class PackageJson implements BuilderInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var PackageJsonGenerator
     */
    private $packageJsonGenerator;

    /**
     * @var PrefabConfig
     */
    private $prefabConfig;

    public function __construct(
        Filesystem $filesystem,
        PackageJsonGenerator $packageJsonGenerator,
        PrefabConfig $prefabConfig
    ) {
        $this->filesystem = $filesystem;
        $this->packageJsonGenerator = $packageJsonGenerator;
        $this->prefabConfig = $prefabConfig;
    }

    public function build()
    {
        $config = $this->prefabConfig->get(null);
        $contents = $this->packageJsonGenerator->generate($config);

        $location = $this->filesystem->getLocation();
        $name = 'package.json';

        $this->filesystem->getFilesystem()->put(
            "{$location}/{$name}",
            $contents
        );
    }
}