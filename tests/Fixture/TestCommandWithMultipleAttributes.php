<?php

declare(strict_types=1);

namespace BotTest\Fixture;

use Bot\Attribute\Command;
use Bot\Command\CommandInterface;
use Bot\DTO\Update\MessageUpdateDTO;

#[Command('first', 'First command')]
#[Command('second', 'Second command')]
final class TestCommandWithMultipleAttributes implements CommandInterface
{
    public function handle(MessageUpdateDTO $update): void
    {
    }
}
