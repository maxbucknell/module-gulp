<?php

namespace MaxBucknell\Gulp\Model\Builder;

use MaxBucknell\Gulp\Api\BuilderInterface;
use MaxBucknell\Gulp\Model\Filesystem;
use MaxBucknell\Gulp\Model\Generator\Gulpfile as GulpfileGenerator;
use MaxBucknell\Gulp\Model\Config\Data as GulpConfig;

class Gulpfile implements BuilderInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var GulpfileGenerator
     */
    private $gulpfileGenerator;

    /**
     * @var GulpConfig
     */
    private $gulpConfig;

    public function __construct(
        Filesystem $filesystem,
        GulpfileGenerator $gulpfileGenerator,
        GulpConfig $gulpConfig
    ) {
        $this->filesystem = $filesystem;
        $this->gulpfileGenerator = $gulpfileGenerator;
        $this->gulpConfig = $gulpConfig;
    }

    public function build()
    {
        $config = $this->gulpConfig->get('tasks');
        $contents = $this->gulpfileGenerator->generate($config);

        $location = $this->filesystem->getLocation();
        $name = 'gulpfile.js';

        $this->filesystem->getFilesystem()->put(
            "{$location}/{$name}",
            $contents
        );
    }
}