<?php

declare(strict_types=1);

namespace Bot\Keyboard\Buttons;

/**
 * @extends \Bot\Keyboard\Buttons\Button<\Bot\Keyboard\Buttons\InlineButton>
 */
class InlineButton extends Button
{
    public ?string $callbackData = null;

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
            'text' => $this->text,
            'callback_data' => $this->callbackData,
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getRequiredFields(): array
    {
        return array_merge(parent::getRequiredFields(), ['callbackData']);
    }
}
