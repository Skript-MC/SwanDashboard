<?php

namespace App\Repository;

use App\Document\DiscordUser;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class DiscordUserRepository extends DocumentRepository
{
    public function updateUser(DiscordUser $user): void
    {
        $this->createQueryBuilder()
            ->findAndUpdate()
            ->field('userId')->equals($user->getUserId())
            ->field('username')->set($user->getUsername())
            ->field('avatarUrl')->set($user->getAvatarUrl())
            ->field('roles')->set($user->getRoles())
            ->getQuery()
            ->execute();
    }

    public function findOneById(string $userId): ?DiscordUser
    {
        return $this->findOneBy(['userId' => $userId]);
    }
}
