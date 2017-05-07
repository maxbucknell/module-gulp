<?php

namespace MaxBucknell\Gulp\Console\Command;


use MaxBucknell\Gulp\Api\BuilderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Build extends Command
{
    /**
     * @var BuilderInterface
     */
    private $builder;

    public function __construct(
        BuilderInterface $builder,
        $name = null
    ) {
        $this->builder = $builder;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('setup:gulp:build');
        $this->setDescription('Generate Gulpfile and required build assets.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->builder->build();
    }
}