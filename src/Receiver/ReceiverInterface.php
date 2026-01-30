<?php

declare(strict_types=1);

namespace Bot\Receiver;

use Bot\Update;

interface ReceiverInterface
{
    /**
     * @param \Bot\Update $update
     * @return bool
     */
    public function supports(Update $update): bool;
}
