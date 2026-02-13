<?php

declare(strict_types=1);

namespace Bot\Keyboard;

class ReplyKeyboard extends Keyboard
{
    protected bool $resizeKeyboard = true;
    protected bool $oneTimeKeyboard = false;

    /**
     * @param bool $value
     * @return self
     */
    public function setResize(bool $value): self
    {
        $this->resizeKeyboard = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'keyboard' => $this->__serialize(),
            'resize_keyboard' => $this->resizeKeyboard,
            'one_time_keyboard' => $this->oneTimeKeyboard,
        ];
    }
}
