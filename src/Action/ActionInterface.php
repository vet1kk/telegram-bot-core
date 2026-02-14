<?php

declare(strict_types=1);

namespace Bot\Action;

use Bot\DTO\Update\CallbackQueryUpdateDTO;

interface ActionInterface
{
    /**
     * @param \Bot\DTO\Update\CallbackQueryUpdateDTO $update
     * @param array $params
     * @return void
     */
    public function handle(CallbackQueryUpdateDTO $update, array $params = []): void;
}
