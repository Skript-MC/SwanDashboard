<?php

namespace App\DataFixtures;

use App\Document\MessageHistory;
use App\Document\User;
use Doctrine\Bundle\MongoDBBundle\Fixture\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class MessageHistoryFixtures
 * @package App\DataFixtures
 */
class MessageHistoryFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $user = $manager->getRepository(User::class)->findOneBy(['_id' => 191495299884122112]);

        $deletedMessage = new MessageHistory();
        $deletedMessage->setUser($user);
        $deletedMessage->setMessageId(806625253429870642);
        $deletedMessage->setChannelId(780877192753184868);
        $deletedMessage->setOldContent('Hello!');
        $deletedMessage->setEditions(['Feet', 'Pineapple']);
        $deletedMessage->setNewContent(null);

        $manager->persist($deletedMessage);
        $manager->flush();

        $editedMessage = new MessageHistory();
        $editedMessage->setUser($user);
        $editedMessage->setMessageId(806625253429570671);
        $editedMessage->setChannelId(780877192753184868);
        $editedMessage->setOldContent('Hi!');
        $editedMessage->setEditions(['Hello!', 'Hey!']);
        $editedMessage->setNewContent('How are you?');

        $manager->persist($editedMessage);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }

}
