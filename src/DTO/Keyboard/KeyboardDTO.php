<?php

declare(strict_types=1);

namespace Bot\DTO\Keyboard;

use Bot\DTO\DTO;
use Bot\DTO\Keyboard\Buttons\ButtonDTO;

/**
 * @template T of \Bot\DTO\Keyboard\KeyboardDTO
 * @extends \Bot\DTO\DTO<T>
 */
abstract class KeyboardDTO extends DTO
{
    /** @var \Bot\DTO\Keyboard\Buttons\ButtonDTO[][] */
    public array $panel = [];

    protected array $required = [
        'panel',
    ];

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data = [], bool $validate = true): static
    {
        /** @var \Bot\DTO\Keyboard\KeyboardDTO $keyboard */
        $keyboard = parent::fromArray($data, false);

        $buttons = $data['buttons'] ?? [];

        foreach ($buttons as $line) {
            if (!is_array($line)) {
                continue;
            }

            $line = array_filter($line, static fn(mixed $b) => $b instanceof ButtonDTO);

            if (count($line) > 0) {
                $keyboard->panel[] = array_values($line);
            }
        }

        if ($validate) {
            $keyboard->validate();
        }

        return $keyboard;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return array_map(static function (array $line) {
            return array_map(static fn(ButtonDTO $b) => $b->jsonSerialize(), $line);
        }, $this->panel);
    }
}
