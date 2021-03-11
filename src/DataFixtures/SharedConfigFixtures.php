<?php

namespace App\DataFixtures;

use App\Document\SharedConfig;
use Doctrine\Bundle\MongoDBBundle\Fixture\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Class SharedConfigFixtures
 * @package App\DataFixtures
 */
class SharedConfigFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $archivedChannels = new SharedConfig();
        $archivedChannels->setName('logged-channels');
        $archivedChannels->setValue([780877192753184868]);

        $manager->persist($archivedChannels);
        $manager->flush();
    }
}
