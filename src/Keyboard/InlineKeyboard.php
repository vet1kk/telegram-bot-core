<?php

declare(strict_types=1);

namespace Bot\Keyboard;

/**
 * @extends \Bot\Keyboard\Keyboard<\Bot\Keyboard\InlineKeyboard>
 */
class InlineKeyboard extends Keyboard
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
