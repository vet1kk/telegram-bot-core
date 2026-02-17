<?php

declare(strict_types=1);

namespace Bot\Keyboard\Buttons;

interface ButtonInterface
{
    /**
     * @return array
     */
    public function jsonSerialize(): array;

    /**
     * @return bool
     */
    public function isValid(): bool;
}
