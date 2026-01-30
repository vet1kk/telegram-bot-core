<?php

declare(strict_types=1);

namespace Bot;

class Update
{
    public function __construct(protected array $data)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function raw(): array
    {
        return $this->data;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getMessage(): ?array
    {
        return $this->data['message'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->getMessage()['text'] ?? null;
    }

    /**
     * @return int|null
     */
    public function getChatId(): ?int
    {
        return $this->getMessage()['chat']['id'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->data['type'] ?? null;
    }
}
