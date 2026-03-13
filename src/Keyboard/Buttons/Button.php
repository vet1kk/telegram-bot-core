<?php

declare(strict_types=1);

namespace Bot\Keyboard\Buttons;

/**
 * @psalm-consistent-constructor
 */
abstract class Button implements ButtonInterface
{
    protected ?string $text = null;

    /**
     * @return static
     */
    public static function create(): static
    {
        return new static();
    }

    /**
     * @param string $text
     * @return $this
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
     * @return list<string>
     */
    protected function getRequiredFields(): array
    {
        return ['text'];
    }
}
