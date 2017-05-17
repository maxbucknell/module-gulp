<?php

namespace MaxBucknell\Gulp\Console\Command;


use Magento\Framework\App\State;
use Magento\Store\Api\StoreRepositoryInterface;
use MaxBucknell\Gulp\Api\DataProviderInterface;
use MaxBucknell\Gulp\Model\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Run extends Command
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var DataProviderInterface
     */
    private $dataProvider;

    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var State
     */
    private $state;

    public function __construct(
        State $state,
        Filesystem $filesystem,
        DataProviderInterface $dataProvider,
        StoreRepositoryInterface $storeRepository,
        $name = null
    )
    {
        parent::__construct($name);
        $this->filesystem = $filesystem;
        $this->dataProvider = $dataProvider;
        $this->storeRepository = $storeRepository;
        $this->state = $state;
    }

    protected function configure()
    {
        $this->setName('setup:gulp:run');
        $this->setDescription('Run a Gulp command');
        $this->addOption(
            'store',
            's',
            InputOption::VALUE_OPTIONAL,
            'Store against which to run command.',
            'default'
        );
        $this->addArgument(
            'gulp-command',
            InputArgument::OPTIONAL,
            'Command to run',
            'default'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->setAreaCode('frontend');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            // intentionally left empty
        }

        $storeCode = $input->getOption('store');
        $store = $this->storeRepository->get($storeCode);
        $data = $this->dataProvider->getData($store);

        $encodedData = \json_encode($data, JSON_FORCE_OBJECT);
        $gulpCommand = $input->getArgument('gulp-command');

        $directory = $this->filesystem->getAbsoluteLocation();
        chdir($directory);

        $command = <<<CMD
NODE_PATH=. ./node_modules/.bin/gulp --magento='{$encodedData}' {$gulpCommand};
CMD;

        passthru($command);
    }
}