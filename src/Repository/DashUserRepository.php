<?php

namespace App\Repository;

use App\Document\DashUser;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\MongoDBException;

class DashUserRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DashUser::class);
    }

    /**
     * @throws MongoDBException
     */
    public function updateUser(DashUser $user): void
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

    public function findOneById(string $userId): ?DashUser
    {
        return $this->findOneBy(['userId' => $userId]);
    }
}
