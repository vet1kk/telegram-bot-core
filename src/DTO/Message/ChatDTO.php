<?php

declare(strict_types=1);

namespace Bot\DTO\Message;

use Bot\DTO\DTO;

/**
 * @extends \Bot\DTO\DTO<\Bot\DTO\Message\ChatDTO>
 */
class ChatDTO extends DTO
{
    public int|string|null $id = null;
    public ?string $type = null;
    public ?string $first_name = null;
    public ?string $last_name = null;
    public ?string $username = null;

    protected array $required = [
        'id',
        'type',
        'first_name'
    ];
}
