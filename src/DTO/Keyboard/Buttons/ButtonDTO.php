<?php

declare(strict_types=1);

namespace Bot\DTO\Keyboard\Buttons;

use Bot\DTO\DTO;

/**
 * @template T of \Bot\DTO\Keyboard\Buttons\ButtonDTO
 * @extends \Bot\DTO\DTO<T>
 */
abstract class ButtonDTO extends DTO
{
    public ?string $text;

    protected array $required = [
        'text',
    ];
}
