<?php

declare(strict_types=1);

namespace Bot\Command;

use Bot\Update;

class CommandManager
{
    /**
     * @var array<string, CommandInterface>
     */
    protected array $commands = [];

    /**
     * @param \Bot\Command\CommandInterface $command
     * @return static
     */
    public function register(CommandInterface $command): static
    {
        $this->commands[$command->getName()] = $command;

        return $this;
    }

    /**
     * @param \Bot\Update $update
     * @return ?\Bot\Command\CommandInterface
     */
    public function resolve(Update $update): ?CommandInterface
    {
        if ($update->getType() !== 'message') {
            return null;
        }

        $text = $update->getText() ?? '';
        if (!str_starts_with($text, '/')) {
            return null;
        }

        $name = explode(' ', ltrim($text, '/'))[0];

        return $this->commands[$name] ?? null;
    }
}
