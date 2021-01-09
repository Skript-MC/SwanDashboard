<?php

namespace App\Discord;

use League\OAuth2\Client\Tool\ArrayAccessorTrait;

class DiscordRole
{

    use ArrayAccessorTrait;

    private array $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    /**
     * Returns the role identifier.
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->getValueByKey($this->response, 'id');
    }

    /**
     * Returns the role name.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->getValueByKey($this->response, 'name');
    }

    /**
     * Returns the role hexadecimal color code.
     *
     * @return int|null
     */
    public function getColor(): ?int
    {
        return $this->getValueByKey($this->response, 'color');
    }

    public function getPosition(): ?int
    {
        return  $this->getValueByKey($this->response, 'position');
    }

    // TODO: Implement others functions: https://discord.com/developers/docs/topics/permissions#role-object

    /**
     * Returns all of the role details available as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->response;
    }
}
