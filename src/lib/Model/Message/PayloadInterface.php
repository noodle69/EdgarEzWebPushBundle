<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Edgar\EzWebPush\Model\Message;

interface PayloadInterface
{
    /**
     * Return a string payload, which can be a simple text or a JSON.
     *
     * @return string
     */
    public function __toString(): string;
}
