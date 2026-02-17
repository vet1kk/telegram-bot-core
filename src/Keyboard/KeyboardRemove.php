<?php

declare(strict_types=1);

namespace Bot\Keyboard;

class KeyboardRemove implements KeyboardInterface
{
    protected bool $remove = true;
    protected bool $selective = false;

    /**
     * @return self
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * @param bool $remove
     * @return $this
     */
    public function setRemove(bool $remove): self
    {
        $this->remove = $remove;

        return $this;
    }

    /**
     * @param bool $selective
     * @return self
     */
    public function setSelective(bool $selective): self
    {
        $this->selective = $selective;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'remove_keyboard' => $this->remove,
            'selective' => $this->selective,
        ];
    }

    /**
     * @inheritDoc
     */
    public function isValid(): bool
    {
        return true;
    }
}
