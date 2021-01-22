<?php

namespace App\Discord;

class DiscordEmbed
{
    protected ?string $title = null;

    protected string $type = 'rich';

    protected ?string $description = null;

    protected ?string $url = null;

    protected ?string $timestamp = null;

    protected ?int $color = null;

    protected ?array $footer = null;

    protected ?array $image = null;

    protected ?array $thumbnail = null;

    protected ?string $provider = null;

    protected ?array $author = null;

    protected ?array $fields = null;


    public function title(string $title, ?string $url = ''): self
    {
        $this->title = $title;
        $this->url = $url;
        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function timestamp(string $timestamp): self
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    public function color(int $color): self
    {
        $this->color = $color;
        return $this;
    }

    public function url(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function footer(string $text, string $icon_url = ''): self
    {
        $this->footer = [
            'text' => $text,
            'icon_url' => $icon_url,
        ];
        return $this;
    }

    public function image(string $url): self
    {
        $this->image = [
            'url' => $url,
        ];
        return $this;
    }

    public function author(string $name, ?string $url = '', ?string $icon_url = ''): self
    {
        $this->author = [
            'name' => $name,
            'url' => $url,
            'icon_url' => $icon_url,
        ];
        return $this;
    }

    public function field(string $name, string $value, bool $inline = false): self
    {
        $this->fields[] = [
            'name' => $name,
            'value' => $value,
            'inline' => $inline,
        ];
        return $this;
    }

    public static function create(): self
    {
        return new DiscordEmbed();
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'type' => $this->type,
            'description' => $this->description,
            'url' => $this->url,
            'color' => $this->color,
            'footer' => $this->footer,
            'image' => $this->image,
            'thumbnail' => $this->thumbnail,
            'timestamp' => $this->timestamp,
            'author' => $this->author,
            'fields' => $this->fields,
        ];
    }

}
