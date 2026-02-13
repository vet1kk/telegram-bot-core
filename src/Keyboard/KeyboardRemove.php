<?php

declare(strict_types=1);

namespace Bot\Keyboard;

class KeyboardRemove implements \JsonSerializable
{
    protected bool $selective = false;

    /**
     * @return self
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * @param bool $value
     * @return self
     */
    public function setSelective(bool $value): self
    {
        $this->selective = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'remove_keyboard' => true,
            'selective' => $this->selective
        ];
    }
}
