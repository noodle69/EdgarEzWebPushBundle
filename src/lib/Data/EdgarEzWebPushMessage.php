<?php

namespace Edgar\EzWebPush\Data;

class EdgarEzWebPushMessage
{
    private $userIdentifier;

    private $message;

    private $locationId;

    public function __construct(
        ?string $userIdentifier = null,
        ?string $message = null,
        ?int $locationId = null
    ) {
        $this->userIdentifier = $userIdentifier;
        $this->message = $message;
        $this->locationId = $locationId;
    }

    public function setUserIdentifier(string $userIdentifier): self
    {
        $this->userIdentifier = $userIdentifier;
        return $this;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function setLocationId(int $locationId): self
    {
        $this->locationId = $locationId;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getLocationId(): int
    {
        return $this->locationId;
    }
}
