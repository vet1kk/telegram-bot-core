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
}
