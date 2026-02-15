<?php

declare(strict_types=1);

namespace Bot\Event\Events;

use Bot\Event\EventInterface;

class UnhandledEvent implements EventInterface
{
    /**
     * @param mixed $rawUpdate
     */
    public function __construct(protected mixed $rawUpdate)
    {
    }

    /**
     * @return array
     */
    public function getUpdate(): mixed
    {
        return $this->rawUpdate;
    }
}
