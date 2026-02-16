<?php

declare(strict_types=1);

namespace Bot\Command;

use Bot\DTO\Update\MessageUpdateDTO;

interface CommandManagerInterface
{
    /**
     * @param string $commandClass
     * @return self
     */
    public function register(string $commandClass): self;

    /**
     * @param \Bot\DTO\Update\MessageUpdateDTO $update
     * @return \Bot\Command\CommandInterface|null
     */
    public function resolve(MessageUpdateDTO $update): ?CommandInterface;

    /**
     * @param \Bot\DTO\Update\MessageUpdateDTO $update
     * @return void
     */
    public function handle(MessageUpdateDTO $update): void;

    /**
     * Get a list of registered commands with their descriptions.
     * The array keys are command names and the values are their descriptions.
     *
     * @return array<string, string>
     */
    public function getCommands(): array;
}
