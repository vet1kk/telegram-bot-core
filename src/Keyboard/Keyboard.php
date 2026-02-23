<?php

declare(strict_types=1);

namespace Bot\Keyboard;

use Bot\Keyboard\Buttons\ButtonInterface;

/**
 * @template T of \Bot\Keyboard\Keyboard
 */
abstract class Keyboard implements KeyboardInterface
{
    /** @var \Bot\Keyboard\Buttons\ButtonInterface[][] */
    protected array $panel = [];

    /**
     * @return static
     * @psalm-return T
     */
    public static function create(): static
    {
        return new static();
    }

    /**
     * @param \Bot\Keyboard\Buttons\ButtonInterface $button
     * @param int $line Line number (starting from 1)
     * @return $this
     * @psalm-return T
     */
    public function addButton(ButtonInterface $button, int $line = 1): static
    {
        if (!$button->isValid()) {
            throw new \LogicException('The provided button structure is invalid');
        }
        $line = max(1, $line);
        $index = min(count($this->panel), $line - 1);

        if (!isset($this->panel[$index])) {
            $this->panel[$index] = [];
        }

        $this->panel[$index][] = $button;

        return $this;
    }

    /**
     * @param \Bot\Keyboard\Buttons\ButtonInterface[] $buttons
     * @param int $line Line number (starting from 1)
     * @return $this
     * @psalm-return T
     */
    public function addButtons(array $buttons, int $line = 1): static
    {
        foreach ($buttons as $button) {
            if (!$button instanceof ButtonInterface) {
                throw new \LogicException('All elements in the buttons array must implement ButtonInterface');
            }
            $this->addButton($button, $line);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return array_map(static function (array $line): array {
            return array_map(static fn(ButtonInterface $b): array => $b->jsonSerialize(), $line);
        }, $this->panel);
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return !empty($this->panel) && !empty($this->panel[0]);
    }
}
