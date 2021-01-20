<?php

namespace App\Discord;

use League\OAuth2\Client\Tool\ArrayAccessorTrait;

class DiscordCategory
{

    use ArrayAccessorTrait;

    private array $data;

    private int $id;

    private string $name;

    private array $channels;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->name = $this->getValueByKey($this->data, 'name');
        $this->id = $this->getValueByKey($this->data, 'id');
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function addChannel(DiscordChannel $channel)
    {
        $this->channels[] = $channel;
    }

    public function getChannels(): array
    {
        return $this->channels;
    }

}
