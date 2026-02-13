<?php

declare(strict_types=1);

namespace Bot\Receiver;

use Bot\Update;

class CallbackQueryReceiver implements ReceiverInterface
{
    /**
     * @inheritDoc
     */
    public function supports(Update $update): bool
    {
        return $update->getType() === Update::TYPE_CALLBACK_QUERY;
    }
}
