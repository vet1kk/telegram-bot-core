<?php

declare(strict_types=1);

namespace Bot\Command;

use Bot\Update;

class CommandManager
{
    /**
     * @var array<string, CommandInterface>
     */
    protected static array $commands = [];

    /**
     * @return static
     */
    public static function create(): static
    {
        return new static();
    }

    /**
     * @param CommandInterface $command
     * @return static
     */
    public function register(CommandInterface $command): static
    {
        static::$commands[$command->getName()] = $command;

        return $this;
    }

    /**
     * @param \Bot\Update $update
     * @return ?\Bot\Command\CommandInterface
     */
    public static function resolve(Update $update): ?CommandInterface
    {
        if ($update->getType() !== 'message') {
            return null;
        }

        if (!str_starts_with($update->getText(), '/')) {
            return null;
        }

        $name = explode(' ', ltrim($update->getText(), '/'))[0];

        return static::$commands[$name] ?? null;
    }
}
