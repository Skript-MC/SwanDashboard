<?php

namespace App\Document\Moderation;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class SanctionInformations
 * @package App\Document\Moderation
 * @MongoDB\EmbeddedDocument()
 */
class SanctionInformations
{
    /**
     * @MongoDB\Field(type="bool")
     */
    protected ?bool $shouldAutobanIfNoMessages;

    /**
     * @MongoDB\Field(type="string")
     */
    protected ?string $banChannelId;

    /**
     * @return bool|null
     */
    public function getShouldAutobanIfNoMessages(): ?bool
    {
        return $this->shouldAutobanIfNoMessages;
    }

    /**
     * @param bool|null $shouldAutobanIfNoMessages
     */
    public function setShouldAutobanIfNoMessages(?bool $shouldAutobanIfNoMessages): void
    {
        $this->shouldAutobanIfNoMessages = $shouldAutobanIfNoMessages;
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
     */
    public function setBanChannelId(?string $banChannelId): void
    {
        $this->banChannelId = $banChannelId;
    }

}
