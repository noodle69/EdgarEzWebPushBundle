<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Edgar\EzWebPushBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EdgarEzWebPushEndpoint.
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

    /**
     * @var string
     *
     * @ORM\Column(name="public_key", type="text", nullable=false)
     */
    private $publicKey;

    /**
     * @var string
     *
     * @ORM\Column(name="auth_token", type="string",length=255, nullable=false)
     */
    private $authToken;

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

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getAuthToken(): string
    {
        return $this->authToken;
    }

    /**
     * @param int $userId
     *
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

    public function setPublicKey(string $publicKey): self
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    public function setAuthToken(string $authToken): self
    {
        $this->authToken = $authToken;

        return $this;
    }
}
