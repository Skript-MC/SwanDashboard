<?php

namespace App\Discord;

use League\OAuth2\Client\Tool\ArrayAccessorTrait;

class DiscordGuild
{

    use ArrayAccessorTrait;

    private array $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    /**
     * Returns the approximate number of members in this guild
     *
     * @return int|null
     */
    public function getMemberCount(): ?int
    {
        return $this->getValueByKey($this->response, 'approximate_member_count');
    }

    /**
     * Returns the approximate number of online members in this guild
     *
     * @return int|null
     */
    public function getPresenceCount(): ?int
    {
        return $this->getValueByKey($this->response, 'approximate_presence_count');
    }

    // TODO: Implement others functions: https://discord.com/developers/docs/resources/guild#guild-preview-object

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
