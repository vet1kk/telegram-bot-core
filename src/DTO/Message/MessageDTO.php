<?php

declare(strict_types=1);

namespace Bot\DTO\Message;

use Bot\DTO\DTO;

/**
 * @extends \Bot\DTO\DTO<\Bot\DTO\Message\MessageDTO>
 */
class MessageDTO extends DTO
{
    public ?int $message_id = null;
    public ?ChatDTO $chat = null;
    public ?string $text = null;
    public ?UserDTO $from = null;
    public ?int $date = null;

    protected array $required = [
        'message_id',
        'chat',
        'text',
    ];
}
