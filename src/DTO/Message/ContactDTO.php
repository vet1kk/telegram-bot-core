<?php

declare(strict_types=1);

namespace Bot\DTO\Message;

use Bot\DTO\DTO;

/**
 * @extends \Bot\DTO\DTO<\Bot\DTO\Message\ContactDTO>
 */
class ContactDTO extends DTO
{
    public ?int $user_id = null;
    public ?string $phone_number = null;
    public ?string $first_name = null;

    protected array $required = [
        'phone_number',
        'first_name',
    ];
}
