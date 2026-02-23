<?php

declare(strict_types=1);

namespace Bot\Http\Message;

class MessageFactory implements MessageFactoryInterface
{
    /**
     * @return \Bot\Http\Message\SendMessageInterface
     */
    public function create(): SendMessageInterface
    {
        return SendMessage::create();
    }
}
