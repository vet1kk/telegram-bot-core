<?php

declare(strict_types=1);

namespace Bot\Keyboard;

class InlineKeyboard extends Keyboard
{
    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'inline_keyboard' => $this->__serialize()
        ];
    }
}
