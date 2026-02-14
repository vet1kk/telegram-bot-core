<?php

declare(strict_types=1);

namespace Bot\DTO\Update;

use Bot\DTO\Message\CallbackQueryDTO;

/**
 * @extends \Bot\DTO\Update\UpdateDTO<\Bot\DTO\Message\CallbackQueryDTO>
 */
class CallbackQueryUpdateDTO extends UpdateDTO
{
    public ?CallbackQueryDTO $callback_query = null;

    protected array $required = [
        'callback_query',
    ];

    /**
     * @return int|null
     */
    public function getChatId(): ?int
    {
        return $this->callback_query?->message?->chat->id
            ?? $this->callback_query?->from?->id;
    }
}
