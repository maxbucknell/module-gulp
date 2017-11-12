<?php

namespace MaxBucknell\Prefab\Console\Command;


use Magento\Framework\App\State;
use Magento\Store\Api\StoreRepositoryInterface;
use MaxBucknell\Prefab\Api\DataProviderInterface;
use MaxBucknell\Prefab\Model\Filesystem;
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
        $this->setName('setup:prefab:run');
        $this->setDescription('Run a prefab build command');
        $this->addOption(
            'store',
            's',
            InputOption::VALUE_OPTIONAL,
            'Store against which to run command.',
            'default'
        );
        $this->addArgument(
            'commands',
            InputArgument::IS_ARRAY,
            'Commands to run',
            ['build']
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

        $environment = '';
        foreach ($data as $var => $value) {
            $sanitised = \escapeshellarg($value);
            $environment .= "{$var}={$sanitised} ";
        }

        $commands = $input->getArgument('commands');

        $directory = $this->filesystem->getAbsoluteLocation();
        chdir($directory);

        foreach ($commands as $command) {
            $consoleCommand = <<<CMD
NODE_PATH=. {$environment} npm run {$command};
CMD;

            passthru($consoleCommand);
        }
    }
}
