<?php

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
