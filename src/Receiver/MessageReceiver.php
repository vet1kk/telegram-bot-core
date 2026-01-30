<?php

declare(strict_types=1);

namespace Bot\Receiver;

use Bot\Update;

class MessageReceiver implements ReceiverInterface
{
    /**
     * @inheritDoc
     */
    public function supports(Update $update): bool
    {
        return !empty($update->getText());
    }
}
