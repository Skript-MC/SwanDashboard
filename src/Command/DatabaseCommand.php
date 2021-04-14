<?php

namespace App\Command;

use App\Document\SharedConfig;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DatabaseCommand extends Command
{
    protected static $defaultName = 'app:db';
    protected static $defaultDescription = 'Configures the databases, so that some of the panel\'s features are available.';

    private DocumentManager $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $loggedChannels = $this->dm->getRepository(SharedConfig::class)->getLoggedChannels();
        if (!isset($loggedChannels)) {
            $loggedChannels = new SharedConfig();
            $loggedChannels->setName('logged-channels');
            $loggedChannels->setValue([]);
            $this->dm->persist($loggedChannels);
            $io->info('Creation of the logged channels document.');
        }

        try {
            $this->dm->flush();
        } catch (MongoDBException $e) {
            $io->error('An error occurred while updating the databases.');
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
        $io->success('The databases have been updated.');
        return Command::SUCCESS;
    }
}
