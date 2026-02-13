<?php

declare(strict_types=1);

namespace Bot\Keyboard\Buttons;

class InlineButton extends Button
{
    protected ?string $callbackData = null;

    /**
     * @param string $callbackData
     * @return self
     */
    public function setCallbackData(string $callbackData): self
    {
        $this->callbackData = $callbackData;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'text' => $this->getText(),
            'callback_data' => $this->callbackData,
        ];
    }

    /**
     * @inheritDoc
     */
    public function isValid(): bool
    {
        return $this->callbackData !== null;
    }
}
