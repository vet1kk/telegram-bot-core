<?php

declare(strict_types=1);

namespace Bot\Action;

use Bot\DTO\Update\CallbackQueryUpdateDTO;

interface ActionManagerInterface
{
    /**
     * @param string $actionClass
     * @return self
     */
    public function register(string $actionClass): self;

    /**
     * @param \Bot\DTO\Update\CallbackQueryUpdateDTO $update
     * @return ?\Bot\Action\ActionInterface
     */
    public function resolve(CallbackQueryUpdateDTO $update): ?ActionInterface;

    /**
     * @param \Bot\DTO\Update\CallbackQueryUpdateDTO $update
     * @return void
     */
    public function handle(CallbackQueryUpdateDTO $update): void;
}
