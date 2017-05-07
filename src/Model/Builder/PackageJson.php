<?php

namespace MaxBucknell\Gulp\Model\Builder;

use MaxBucknell\Gulp\Api\BuilderInterface;
use MaxBucknell\Gulp\Model\Generator\PackageJson as PackageJsonGenerator;
use MaxBucknell\Gulp\Model\Filesystem;
use MaxBucknell\Gulp\Model\Config\Data as GulpConfig;

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
     * @var GulpConfig
     */
    private $gulpConfig;

    public function __construct(
        Filesystem $filesystem,
        PackageJsonGenerator $packageJsonGenerator,
        GulpConfig $gulpConfig
    ) {
        $this->filesystem = $filesystem;
        $this->packageJsonGenerator = $packageJsonGenerator;
        $this->gulpConfig = $gulpConfig;
    }

    public function build()
    {
        $config = $this->gulpConfig->get('dependencies');
        $contents = $this->packageJsonGenerator->generate($config);

        $location = $this->filesystem->getLocation();
        $name = 'package.json';

        $this->filesystem->getFilesystem()->put(
            "{$location}/{$name}",
            $contents
        );
    }
}