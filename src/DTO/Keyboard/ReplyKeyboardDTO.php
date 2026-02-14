<?php

declare(strict_types=1);

namespace Bot\DTO\Keyboard;

/**
 * @extends \Bot\DTO\Keyboard\KeyboardDTO<\Bot\DTO\Keyboard\ReplyKeyboardDTO
 */
class ReplyKeyboardDTO extends KeyboardDTO
{
    public bool $resizeKeyboard = true;
    public bool $oneTimeKeyboard = false;

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'keyboard' => parent::jsonSerialize(),
            'resize_keyboard' => $this->resizeKeyboard,
            'one_time_keyboard' => $this->oneTimeKeyboard,
        ];
    }
}
