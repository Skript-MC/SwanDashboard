<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @MongoDB\Document(collection="users")
 */
class User implements UserInterface
{
    /**
     * @MongoDB\Id(type="string")
     */
    protected string $id;

    /**
     * @MongoDB\Field(type="int")
     */
    protected int $discordId;

    /**
     * @MongoDB\Field(type="string")
     */
    protected string $username;

    /**
     * @MongoDB\Field(type="string")
     */
    protected string $avatarUrl;

    /**
     * @MongoDB\Field(type="bool")
     */
    protected bool $hasMFA;

    /**
     * @MongoDB\Field(type="collection")
     */
    protected array $roles = [];

    /**
     * @MongoDB\Field(type="collection")
     */
    protected array $discordRoles = [];

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getDiscordId(): int
    {
        return $this->discordId;
    }

    /**
     * @param int $discordId
     */
    public function setDiscordId(int $discordId): void
    {
        $this->discordId = $discordId;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getAvatarUrl(): string
    {
        return $this->avatarUrl;
    }

    /**
     * @param string $avatarUrl
     */
    public function setAvatarUrl(string $avatarUrl): void
    {
        $this->avatarUrl = $avatarUrl;
    }

    /**
     * @return bool
     */
    public function isHasMFA(): bool
    {
        return $this->hasMFA;
    }

    /**
     * @param bool $hasMFA
     */
    public function setHasMFA(bool $hasMFA): void
    {
        $this->hasMFA = $hasMFA;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @return array
     */
    public function getDiscordRoles(): array
    {
        return $this->discordRoles;
    }

    /**
     * @param array $discordRoles
     */
    public function setDiscordRoles(array $discordRoles): void
    {
        $this->discordRoles = $discordRoles;
    }

    public function getPassword(): ?string
    {
        return null;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials()
    {
        return null;
    }
}
