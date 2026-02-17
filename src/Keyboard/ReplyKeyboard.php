<?php

declare(strict_types=1);

namespace Bot\Keyboard;

/**
 * @extends \Bot\Keyboard\Keyboard<\Bot\Keyboard\ReplyKeyboard>
 */
class ReplyKeyboard extends Keyboard
{
    protected bool $resize = true;
    protected bool $oneTime = false;

    /**
     * @param bool $resize
     * @return self
     */
    public function setResize(bool $resize): self
    {
        $this->resize = $resize;

        return $this;
    }

    /**
     * @param bool $oneTime
     * @return self
     */
    public function setOneTime(bool $oneTime): self
    {
        $this->oneTime = $oneTime;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'keyboard' => parent::jsonSerialize(),
            'resize_keyboard' => $this->resize,
            'one_time_keyboard' => $this->oneTime,
        ];
    }
}
