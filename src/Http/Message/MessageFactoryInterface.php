<?php

declare(strict_types=1);

namespace Bot\Http\Message;

interface MessageFactoryInterface
{
    /**
     * @return \Bot\Http\Message\SendMessageInterface
     */
    public function create(): SendMessageInterface;
}
