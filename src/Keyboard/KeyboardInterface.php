<?php

declare(strict_types=1);

namespace Bot\Keyboard;

interface KeyboardInterface extends \JsonSerializable
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
