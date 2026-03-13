<?php

declare(strict_types=1);

namespace Bot\DTO\Update;

use Bot\DTO\Message\CallbackQueryDTO;

class CallbackQueryUpdateDTO extends UpdateDTO
{
    public ?CallbackQueryDTO $callback_query = null;

    /** @var list<string> */
    protected array $required = [
        'callback_query',
    ];

    /**
     * @return int|string|null
     */
    public function getChatId(): int|string|null
    {
        return $this->callback_query?->message?->chat?->id;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->callback_query?->from?->id;
    }
}
