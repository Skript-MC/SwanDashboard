<?php

namespace App\Discord;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class DiscordWebhook
{

    protected const USERNAME = 'Swan Dashboard';

    protected const AVATAR = 'https://github.com/Skript-MC/Swan/raw/v2/assets/logo.png';

    protected string $url;

    protected ?string $message = null;

    protected ?array $embeds = null;

    protected bool $tts = false;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public static function create(string $url): self
    {
        return new DiscordWebhook($url);
    }

    /**
     * @param string $message
     * @return DiscordWebhook
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @param array $embeds
     * @return DiscordWebhook
     */
    public function setEmbeds(array $embeds): self
    {
        $this->embeds = $embeds;
        return $this;
    }

    /**
     * @param bool $tts
     * @return DiscordWebhook
     */
    public function setTts(bool $tts): self
    {
        $this->tts = $tts;
        return $this;
    }

    public function send(): void
    {
        $payload = [
            'username' => self::USERNAME,
            'avatar_url' => self::AVATAR,
            'content' => $this->message,
            'embeds' => array_map(function (DiscordEmbed $embed) {
                return $embed->toArray();
            }, $this->embeds),
            'tts' => false
        ];

        try {
            HttpClient::create()->request('POST', $this->url, ['json' => $payload]);
        } catch (TransportExceptionInterface $e) {}
    }

}
