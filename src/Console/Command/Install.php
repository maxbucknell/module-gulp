<?php

namespace MaxBucknell\Prefab\Console\Command;


use MaxBucknell\Prefab\Model\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Install extends Command
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        Filesystem $filesystem,
        $name = null
    ) {
        parent::__construct($name);

        $this->filesystem = $filesystem;
    }


    protected function configure()
    {
        $this->setName('setup:prefab:install');
        $this->setDescription('Install all NPM dependencies');
        $this->addOption(
            'npm',
            null,
            InputOption::VALUE_NONE,
            'Whether to use npm instead of yarn'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $directory = $this->filesystem->getAbsoluteLocation();

        chdir($directory);
        passthru("npm update");
    }
}