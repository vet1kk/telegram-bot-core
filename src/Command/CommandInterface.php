<?php

declare(strict_types=1);

namespace Bot\Command;

use Bot\DTO\Update\MessageUpdateDTO;

interface CommandInterface
{
    /**
     * @param \Bot\DTO\Update\MessageUpdateDTO $update
     * @return void
     */
    public function handle(MessageUpdateDTO $update): void;
}
