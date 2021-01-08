<?php

namespace App\Discord;

use League\OAuth2\Client\Tool\ArrayAccessorTrait;

class DiscordMember
{

    use ArrayAccessorTrait;

    private array $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    /**
     * Returns the member nickname.
     *
     * @return string|null
     */
    public function getNickname(): ?string
    {
        return $this->getValueByKey($this->response, 'nick');
    }

    /**
     * Returns the member roles as a snowflake array.
     *
     * @return array|null
     */
    public function getRoles(): ?array
    {
        return $this->getValueByKey($this->response, 'roles');
    }

    /**
     * Returns whether the member is deafened in voice channels.
     *
     * @return bool|null
     */
    public function isDeafened(): ?bool
    {
        return $this->getValueByKey($this->response, 'def');
    }

    /**
     * Returns 	whether the member is muted in voice channels.
     *
     * @return bool|null
     */
    public function isMuted(): ?bool
    {
        return $this->getValueByKey($this->response, 'mute');
    }

    /**
     * Returns whether the member has not yet passed the guild's Membership Screening requirements.
     *
     * @return bool|null
     */
    public function isPending(): ?bool
    {
        return $this->getValueByKey($this->response, 'pending');
    }

    /**
     * Returns all of the member details available as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->response;
    }
}
