<?php

declare(strict_types=1);

namespace Bot\DTO\Update;

use Bot\DTO\Message\MessageDTO;

/**
 * @extends \Bot\DTO\Update\UpdateDTO<\Bot\DTO\Update\MessageUpdateDTO>
 */
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

        if (!empty($message)) {
            $data['message'] ??= $message;
        }

        /** @var \Bot\DTO\Update\MessageUpdateDTO $self */
        $self = parent::fromArray($data, $validate);

        return $self;
    }

    /**
     * @return int|null
     */
    public function getChatId(): ?int
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
        return !empty($this->message->edit_date);
    }
}
