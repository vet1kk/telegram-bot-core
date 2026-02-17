<?php

declare(strict_types=1);

namespace Bot\Http\Message;

interface SendMessageInterface
{
    /**
     * @return array
     */
    public function jsonSerialize(): array;
}
