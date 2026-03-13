<?php

declare(strict_types=1);

namespace Bot\DTO\Update;

use Bot\DTO\Message\MessageDTO;

class MessageUpdateDTO extends UpdateDTO
{
    public ?MessageDTO $message = null;

    /** @var list<string> */
    protected array $required = [
        'message',
    ];

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data = [], bool $validate = true): static
    {
        /** @var mixed $message */
        $message = $data['message'] ?? $data['edited_message'] ?? null;

        if ($message !== null && !array_key_exists('message', $data)) {
            $data = ['message' => $message] + $data;
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
