<?php

declare(strict_types=1);

namespace Bot\DTO\Keyboard\Buttons;

/**
 * @extends \Bot\DTO\Keyboard\Buttons\ButtonDTO<\Bot\DTO\Keyboard\Buttons\InlineButtonDTO>
 */
class InlineButtonDTO extends ButtonDTO
{
    public ?string $callback_data = null;

    protected array $required = [
        'callback_data',
    ];

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'text' => $this->text,
            'callback_data' => $this->callback_data,
        ];
    }
}
