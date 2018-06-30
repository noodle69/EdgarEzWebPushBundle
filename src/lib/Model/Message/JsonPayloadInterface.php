<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Edgar\EzWebPush\Model\Message;

interface JsonPayloadInterface extends PayloadInterface, \JsonSerializable
{
    /**
     * @return array
     */
    public function jsonSerialize(): array;

    /**
     * The JSON representation of the current object (usually json_encode($this)).
     *
     * @return string
     */
    public function __toString(): string;
}
