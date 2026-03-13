<?php

declare(strict_types=1);

namespace Bot\DTO\Message;

use Bot\DTO\DTO;

class UserDTO extends DTO
{
    public ?int $id = null;
    public bool $is_bot = false;
    public ?string $first_name = null;
    public ?string $last_name = null;
    public ?string $username = null;
    public ?string $language_code = null;

    /** @var list<string> */
    protected array $required = [
        'id',
        'is_bot',
        'first_name',
    ];
}
