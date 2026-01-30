<?php

declare(strict_types=1);

namespace Bot\Command;

use Bot\Update;

interface CommandInterface
{
    /**
     * @param \Bot\Update $update
     * @return void
     */
    public function handle(Update $update): void;
}
