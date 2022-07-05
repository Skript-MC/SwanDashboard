<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class DashUser
 * @package App\Document
 * @MongoDB\Document(collection="dashusers", repositoryClass="App\Repository\DashUserRepository")
 */
class DashUser implements UserInterface
{
    /**
     * @MongoDB\Id
     */
    private string $panelId;

    /**
     * @MongoDB\Field(type="int")
     */
    private int $discordId;

    /**
     * @MongoDB\Field(type="bool")
     */
    private ?bool $hasMFA = null;

    /**
     * @MongoDB\Field(type="collection")
     */
    private array $roles = [];

    /**
     * @return string
     */
    public function getPanelId(): string
    {
        return $this->panelId;
    }

    /**
     * @param string $panelId
     * @return DashUser
     */
    public function setPanelId(string $panelId): DashUser
    {
        $this->panelId = $panelId;
        return $this;
    }

    /**
     * @return string
     */
    public function getDiscordId(): string
    {
        return $this->discordId;
    }

    /**
     * @param string $discordId
     * @return DashUser
     */
    public function setDiscordId(string $discordId): DashUser
    {
        $this->discordId = $discordId;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getHasMFA(): ?bool
    {
        return $this->hasMFA;
    }

    /**
     * @param bool|null $hasMFA
     * @return DashUser
     */
    public function setHasMFA(?bool $hasMFA): DashUser
    {
        $this->hasMFA = $hasMFA;
        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // Guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    /**
     * @param array $roles
     * @return DashUser
     */
    public function setRoles(array $roles): DashUser
    {
        $this->roles = $roles;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Nothing to do...
    }

    public function getUserIdentifier(): string
    {
        return $this->discordId;
    }
}
