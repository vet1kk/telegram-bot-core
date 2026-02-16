<?php

declare(strict_types=1);

namespace Bot\DTO\Keyboard;

/**
 * @extends \Bot\DTO\Keyboard\KeyboardDTO<\Bot\DTO\Keyboard\InlineKeyboardDTO>
 */
class InlineKeyboardDTO extends KeyboardDTO
{
    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'inline_keyboard' => parent::jsonSerialize(),
        ];
    }
}
