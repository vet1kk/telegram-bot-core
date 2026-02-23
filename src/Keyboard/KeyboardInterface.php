<?php

declare(strict_types=1);

namespace Bot\Keyboard;

interface KeyboardInterface extends \JsonSerializable
{
    /**
     * @return bool
     */
    public function isValid(): bool;
}
