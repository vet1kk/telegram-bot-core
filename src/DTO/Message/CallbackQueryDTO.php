<?php

declare(strict_types=1);

namespace Bot\DTO\Message;

use Bot\DTO\DTO;

/**
 * @extends \Bot\DTO\DTO<\Bot\DTO\Message\CallbackQueryDTO>
 */
class CallbackQueryDTO extends DTO
{
    public ?string $id = null;
    public ?string $data = null;
    public ?MessageDTO $message = null;
    public ?UserDTO $from = null;

    protected array $required = [
        'id',
        'data',
    ];
}
