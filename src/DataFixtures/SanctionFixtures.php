<?php

namespace App\DataFixtures;

use App\Document\Moderation\ConvictedUser;
use App\Document\Moderation\Sanction;
use App\Document\Moderation\SanctionInformations;
use Doctrine\Bundle\MongoDBBundle\Fixture\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SanctionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $convictedUser = $manager->getRepository(ConvictedUser::class)->findOneBy(['memberId' => '191495299884122112']);

        $sanction = new Sanction();
        $sanction->setType('ban');
        $sanction->setUser($convictedUser);
        $sanction->setMemberId(191495299884122112);
        $sanction->setModerator(752259261475586139);
        $sanction->setDuration(100000);
        $sanction->setStart(date_create()->setTimestamp(1611429059733));
        $sanction->setFinish(date_create()->setTimestamp(1611429179733));
        $sanction->setRevoked(false);
        $sanction->setReason('I wanted to!');
        $sanction->setSanctionId('2uf7WU6x');
        $sanction->setInformations(new SanctionInformations());

        $manager->persist($sanction);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ConvictedUserFixtures::class
        ];
    }

}
