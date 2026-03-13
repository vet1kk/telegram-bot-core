<?php

declare(strict_types=1);

namespace Bot\DTO\Message;

use Bot\DTO\DTO;

class CallbackQueryDTO extends DTO
{
    public ?string $id = null;
    public ?string $data = null;
    public ?MessageDTO $message = null;
    public ?MessageDTO $reply_to_message = null;
    public ?UserDTO $from = null;
    public ?string $chat_instance = null;

    /** @var list<string> */
    protected array $required = [
        'id',
        'from',
        'chat_instance',
    ];
}
