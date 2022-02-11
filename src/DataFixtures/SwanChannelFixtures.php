<?php

namespace App\DataFixtures;

use App\Document\SwanChannel;
use Doctrine\Bundle\MongoDBBundle\Fixture\Fixture;
use Doctrine\Persistence\ObjectManager;

class SwanChannelFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $archivedChannels = new SwanChannel();
        $archivedChannels->setName('blabla');
        $archivedChannels->setChannelId(780877192753184868);
        $archivedChannels->setLogged(true);

        $manager->persist($archivedChannels);
        $manager->flush();
    }
}
