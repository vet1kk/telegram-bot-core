<?php

declare(strict_types=1);

namespace Bot\Event\Events;

use Bot\Command\CommandInterface;
use Bot\DTO\Update\MessageUpdateDTO;
use Bot\Event\EventInterface;

class CommandHandledEvent implements EventInterface
{
    /**
     * @param CommandInterface $command
     * @param \Bot\DTO\Update\MessageUpdateDTO $update
     */
    public function __construct(protected CommandInterface $command, protected MessageUpdateDTO $update)
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
     * @return \Bot\DTO\Update\MessageUpdateDTO
     */
    public function getUpdate(): MessageUpdateDTO
    {
        return $this->update;
    }
}
