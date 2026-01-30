<?php

declare(strict_types=1);

namespace Bot\Event\Events;

use Bot\Event\EventInterface;
use Bot\Update;

class ReceivedEvent implements EventInterface
{
    /**
     * @param Update $update
     */
    public function __construct(protected Update $update)
    {
    }

    /**
     * @return Update
     */
    public function getUpdate(): Update
    {
        return $this->update;
    }
}
