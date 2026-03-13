<?php

declare(strict_types=1);

namespace Bot\Keyboard\Buttons;

interface ButtonInterface extends \JsonSerializable
{
    /**
     * @return array<array-key, mixed>
     */
    public function jsonSerialize(): array;

    /**
     * @return bool
     */
    public function isValid(): bool;
}
