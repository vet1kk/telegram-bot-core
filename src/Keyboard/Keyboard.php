<?php

declare(strict_types=1);

namespace Bot\Keyboard;

use Bot\Keyboard\Buttons\Button;

abstract class Keyboard implements \JsonSerializable
{
    /** @var \Bot\Keyboard\Buttons\Button[][] */
    protected array $keyboard = [];

    protected function __construct()
    {
    }

    /**
     * @return static
     */
    public static function create(): static
    {
        return new static();
    }

    /**
     * @param Button[] $buttons
     */
    public function addButtons(array $buttons): self
    {
        $buttons = array_filter($buttons, static fn($b) => $b instanceof Button && $b->isValid());
        $this->keyboard[] = $buttons;

        return $this;
    }

    /**
     * @return array
     */
    public function __serialize(): array
    {
        return array_map(static function (array $line) {
            return array_map(static fn(Button $b) => $b->jsonSerialize(), $line);
        }, $this->keyboard);
    }

    abstract public function jsonSerialize(): mixed;
}
