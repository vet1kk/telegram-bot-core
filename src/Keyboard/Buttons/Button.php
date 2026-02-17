<?php

declare(strict_types=1);

namespace Bot\Keyboard\Buttons;

/**
 * @template T of \Bot\Keyboard\Buttons\Button
 */
abstract class Button implements ButtonInterface
{
    protected ?string $text = null;

    /**
     * @return static
     * @psalm-return T
     */
    public static function create(): static
    {
        return new static();
    }

    /**
     * @param string $text
     * @return $this
     * @psalm-return T
     */
    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        foreach ($this->getRequiredFields() as $field) {
            if (!isset($this->{$field})) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array
     */
    protected function getRequiredFields(): array
    {
        return ['text'];
    }
}
