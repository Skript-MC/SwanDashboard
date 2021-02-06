<?php

namespace App\DataFixtures;

use App\Document\Moderation\ConvictedUser;
use Doctrine\Bundle\MongoDBBundle\Fixture\Fixture;
use Doctrine\Persistence\ObjectManager;

class ConvictedUserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $convictedUser = new ConvictedUser();
        $convictedUser->setMemberId('191495299884122112');

        $manager->persist($convictedUser);
        $manager->flush();
    }
}
