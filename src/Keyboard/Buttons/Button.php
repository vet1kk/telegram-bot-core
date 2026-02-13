<?php

declare(strict_types=1);

namespace Bot\Keyboard\Buttons;

abstract class Button implements \JsonSerializable
{
    /**
     * @param string $text
     */
    protected function __construct(protected string $text)
    {
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return static
     */
    public static function create(string $text): static
    {
        return new static($text);
    }

    /**
     * @return array
     */
    abstract public function jsonSerialize(): array;

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return true;
    }
}
