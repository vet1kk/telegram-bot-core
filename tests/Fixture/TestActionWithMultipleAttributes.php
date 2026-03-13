<?php

declare(strict_types=1);

namespace BotTest\Fixture;

use Bot\Action\ActionInterface;
use Bot\Attribute\Action;
use Bot\DTO\Update\CallbackQueryUpdateDTO;

#[Action('first', 'First action')]
#[Action('second', 'Second action')]
final class TestActionWithMultipleAttributes implements ActionInterface
{
    public function handle(CallbackQueryUpdateDTO $update, array $params = []): void
    {
    }
}
