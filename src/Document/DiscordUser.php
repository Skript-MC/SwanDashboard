<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class DiscordUser
 * @package App\Document
 * @MongoDB\Document(collection="discordusers", repositoryClass="App\Repository\DiscordUserRepository")
 */
class DiscordUser implements UserInterface
{
    /**
     * @MongoDB\Id(type="string")
     */
    protected string $id;

    /**
     * @MongoDB\Field(type="string")
     */
    protected string $userId;

    /**
     * @MongoDB\Field(type="string")
     */
    protected string $username;

    /**
     * @MongoDB\Field(type="string")
     */
    protected string $avatarUrl = 'https://cdn.discordapp.com/embed/avatars/0.png';

    /**
     * @MongoDB\Field(type="bool")
     */
    protected ?bool $hasMFA = null;

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
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
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
     * @return bool|null
     */
    public function hasMFA(): ?bool
    {
        return $this->hasMFA;
    }

    /**
     * @param bool|null $hasMFA
     */
    public function setHasMFA(?bool $hasMFA): void
    {
        $this->hasMFA = $hasMFA;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
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

    public function getUserIdentifier(): string
    {
        return $this->username;
    }
}
