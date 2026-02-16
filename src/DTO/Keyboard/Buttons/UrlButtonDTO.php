<?php

declare(strict_types=1);

namespace Bot\DTO\Keyboard\Buttons;

/**
 * @extends \Bot\DTO\Keyboard\Buttons\ButtonDTO<\Bot\DTO\Keyboard\Buttons\UrlButtonDTO>
 */
class UrlButtonDTO extends ButtonDTO
{
    public ?string $url = null;

    protected array $required = [
        'url',
    ];

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'text' => $this->text,
            'url' => $this->url,
        ];
    }
}
