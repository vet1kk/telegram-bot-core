<?php

declare(strict_types=1);

namespace Bot\DTO\Update;

use Bot\DTO\DTO;

/**
 * @template T of \Bot\DTO\Update\UpdateDTO
 * @extends \Bot\DTO\DTO<T>
 */
class UpdateDTO extends DTO
{
    public ?int $update_id = null;

    protected array $required = [
        'update_id',
    ];

    public function getChatId(): ?int
    {
        return null;
    }

    public function getUserId(): ?int
    {
        return null;
    }
}
