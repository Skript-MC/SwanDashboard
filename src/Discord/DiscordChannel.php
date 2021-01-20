<?php

namespace App\Discord;

use League\OAuth2\Client\Tool\ArrayAccessorTrait;

class DiscordChannel
{

    use ArrayAccessorTrait;

    private array $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    /**
     * Returns the channel identifier.
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->getValueByKey($this->response, 'id');
    }

    /**
     * Returns the channel type.
     *
     * 0: GUILD_TEXT
     * 1: DM
     * 2: GUILD_VOICE
     * 3: GROUP_DM
     * 4: GUILD_CATEGORY
     * 5: GUILD_NEWS
     * 6: GUILD_STORE
     *
     * @return int|null
     */
    public function getType(): ?int
    {
        return $this->getValueByKey($this->response, 'type');
    }

    /**
     * Returns the channel position.
     *
     * @return int|null
     */
    public function getPosition(): ?int
    {
        return $this->getValueByKey($this->response, 'position');
    }

    /**
     * Returns the channel name.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->getValueByKey($this->response, 'name');
    }

    /**
     * Returns the channel parent identifier.
     *
     * @return string|null
     */
    public function getParentId(): ?string
    {
        return $this->getValueByKey($this->response, 'parent_id');
    }

    // TODO: Implement others functions https://discord.com/developers/docs/resources/channel

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
