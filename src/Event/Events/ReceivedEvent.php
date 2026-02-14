<?php

declare(strict_types=1);

namespace Bot\Event\Events;

use Bot\DTO\Update\UpdateDTO;
use Bot\Event\EventInterface;

class ReceivedEvent implements EventInterface
{
    /**
     * @param \Bot\DTO\Update\UpdateDTO $update
     */
    public function __construct(protected UpdateDTO $update)
    {
    }

    /**
     * @return \Bot\DTO\Update\UpdateDTO
     */
    public function getUpdate(): UpdateDTO
    {
        return $this->update;
    }
}
