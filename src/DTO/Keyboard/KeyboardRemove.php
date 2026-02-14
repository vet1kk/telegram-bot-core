<?php

declare(strict_types=1);

namespace Bot\DTO\Keyboard;

use Bot\DTO\DTO;

/**
 * @extends \Bot\DTO\DTO<\Bot\DTO\Keyboard\KeyboardRemove>
 */
class KeyboardRemove extends DTO
{
    public bool $remove_keyboard = true;
    public bool $selective = false;
}
