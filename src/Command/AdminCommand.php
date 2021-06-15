<?php


namespace App\Command;


use App\Document\DiscordUser;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AdminCommand extends Command
{
    protected static $defaultName = 'app:admin';
    protected static $defaultDescription = 'Set a previously registered user as administrator.';

    private DocumentManager $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription(self::$defaultDescription)
            ->addArgument('discordId', InputArgument::REQUIRED, 'The user\'s Discord ID.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $repository = $this->dm->getRepository(DiscordUser::class);

        $user = $repository->findOneById($input->getArgument('discordId'));
        if (!isset($user)) {
            $io->error('The user associated with this ID was not found.');
            return Command::FAILURE;
        }

        try {
            $user->setRoles(['ROLE_ADMIN']);
            $repository->updateUser($user);
        } catch (MongoDBException $e) {
            $io->error('An error occurred while updating the user.');
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
        $io->success('The user is now an administrator.');
        return Command::SUCCESS;
    }
}
