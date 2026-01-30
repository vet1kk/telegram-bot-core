<?php

declare(strict_types=1);

namespace Bot\Event\Events;

use Bot\Command\CommandInterface;
use Bot\Event\EventInterface;
use Bot\Update;

class CommandHandledEvent implements EventInterface
{
    /**
     * @param CommandInterface $command
     * @param Update $update
     */
    public function __construct(protected CommandInterface $command, protected Update $update)
    {
    }

    /**
     * @return CommandInterface
     */
    public function getCommand(): CommandInterface
    {
        return $this->command;
    }

    /**
     * @return Update
     */
    public function getUpdate(): Update
    {
        return $this->update;
    }
}
