<?php

namespace App\DataFixtures;

use App\Document\CommandStat;
use Doctrine\Bundle\MongoDBBundle\Fixture\Fixture;
use Doctrine\Persistence\ObjectManager;

class CommandStatFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $commandStat = new CommandStat();
        $commandStat->setCommandId('errorDetails');
        $commandStat->setUses(1);

        $manager->persist($commandStat);
        $manager->flush();
    }
}
