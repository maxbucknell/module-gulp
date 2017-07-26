<?php

namespace MaxBucknell\Gulp\Console\Command;


use MaxBucknell\Gulp\Model\Filesystem;
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
        $this->setName('setup:gulp:install');
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

        $executable = $input->getOption('npm') ? 'npm' : 'yarn';
        $directory = $this->filesystem->getAbsoluteLocation();

        chdir($directory);
        passthru("rm -rf package-lock.json");
        passthru("$executable install");
    }
}