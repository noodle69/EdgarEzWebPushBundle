<?php

namespace Edgar\EzWebPushBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EdgarEzWebPushEndpoint
 *
 * @ORM\Entity(repositoryClass="Edgar\EzWebPush\Repository\EdgarEzWebPushEndpointRepository")
 * @ORM\Table(name="edgar_ez_webpush_endpoint")
 */
class EdgarEzWebPushEndpoint
{
    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     * @ORM\Id
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="endpoint", type="string",length=255, nullable=false)
     * @ORM\Id
     */
    private $endpoint;

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param int $userId
     * @return EdgarEzBookmark
     */
    public function setUserId(?int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function setEndpoint(string $endpoint): self
    {
        $this->endpoint = $endpoint;
        return $this;
    }

}
