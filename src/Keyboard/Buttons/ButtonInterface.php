<?php

declare(strict_types=1);

namespace Bot\Keyboard\Buttons;

interface ButtonInterface extends \JsonSerializable
{
    /**
     * @return bool
     */
    public function isValid(): bool;
}
