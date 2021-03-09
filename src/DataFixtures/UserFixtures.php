<?php

namespace App\DataFixtures;

use App\Document\User;
use Doctrine\Bundle\MongoDBBundle\Fixture\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Class UserFixtures
 * @package App\DataFixtures
 */
class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $adminUser = new User();
        $adminUser->setDiscordId(191495299884122112);
        $adminUser->setUsername('Romitou#9685');
        $adminUser->setDiscordRoles([]);
        $adminUser->setRoles(['ROLE_ADMIN']);
        $adminUser->setHasMFA(true);
        $adminUser->setAvatarUrl('https://cdn.discordapp.com/avatars/191495299884122112/be661ca87476ae6c9913190665db7e59.png');

        $manager->persist($adminUser);
        $manager->flush();

        $user = new User();
        $user->setDiscordId(752259261475586139);
        $user->setUsername('Romi2#0000');
        $user->setDiscordRoles([]);
        $user->setRoles(['ROLE_STAFF']);
        $user->setHasMFA(false);
        $user->setAvatarUrl('https://cdn.discordapp.com/avatars/191495299884122112/be661ca87476ae6c9913190665db7e59.png');

        $manager->persist($user);
        $manager->flush();

        $temporaryUser = new User();
        $temporaryUser->setDiscordId(191495299884122110);
        $temporaryUser->setUsername('Romitwo#0002');
        $temporaryUser->setDiscordRoles([]);
        $temporaryUser->setRoles(['ROLE_USER']);
        $temporaryUser->setHasMFA(false);
        $temporaryUser->setAvatarUrl('https://cdn.discordapp.com/avatars/191495299884122112/be661ca87476ae6c9913190665db7e59.png');

        $manager->persist($temporaryUser);
        $manager->flush();

    }
}
