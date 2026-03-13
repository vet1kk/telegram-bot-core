<?php

declare(strict_types=1);

namespace Bot\DTO\Update;

use Bot\DTO\Message\MessageDTO;

class MessageUpdateDTO extends UpdateDTO
{
    public ?MessageDTO $message = null;

    protected array $required = [
        'message',
    ];

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data = [], bool $validate = true): static
    {
        $message = $data['message'] ?? $data['edited_message'] ?? null;

        if ($message !== null) {
            $data['message'] ??= $message;
        }

        return parent::fromArray($data, $validate);
    }

    /**
     * @return int|string|null
     */
    public function getChatId(): int|string|null
    {
        return $this->message?->chat?->id;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->message?->from?->id;
    }

    /**
     * @return bool
     */
    public function isEdit(): bool
    {
        return $this->message?->edit_date !== null;
    }
}
