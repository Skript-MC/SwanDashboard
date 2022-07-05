<?php

namespace App\Document\Moderation;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class Informations
 * @package App\Document\Moderation
 * @MongoDB\EmbeddedDocument()
 */
class Informations
{
    /**
     * @MongoDB\Field(type="bool")
     */
    private ?bool $shouldAutobanIfNoMessages;

    /**
     * @MongoDB\Field(type="string")
     */
    private ?string $banChannelId;

    /**
     * @MongoDB\Field(type="bool")
     */
    private ?bool $hasSentMessages;

    /**
     * @return bool|null
     */
    public function getShouldAutobanIfNoMessages(): ?bool
    {
        return $this->shouldAutobanIfNoMessages;
    }

    /**
     * @param bool|null $shouldAutobanIfNoMessages
     * @return Informations
     */
    public function setShouldAutobanIfNoMessages(?bool $shouldAutobanIfNoMessages): Informations
    {
        $this->shouldAutobanIfNoMessages = $shouldAutobanIfNoMessages;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBanChannelId(): ?string
    {
        return $this->banChannelId;
    }

    /**
     * @param string|null $banChannelId
     * @return Informations
     */
    public function setBanChannelId(?string $banChannelId): Informations
    {
        $this->banChannelId = $banChannelId;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getHasSentMessages(): ?bool
    {
        return $this->hasSentMessages;
    }

    /**
     * @param bool|null $hasSentMessages
     * @return Informations
     */
    public function setHasSentMessages(?bool $hasSentMessages): Informations
    {
        $this->hasSentMessages = $hasSentMessages;
        return $this;
    }
}
