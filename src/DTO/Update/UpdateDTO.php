<?php

declare(strict_types=1);

namespace Bot\DTO\Update;

use Bot\DTO\DTO;
use Bot\Trait\ReplyTrait;

/**
 * @template T of \Bot\DTO\Update\UpdateDTO
 * @extends \Bot\DTO\DTO<T>
 */
class UpdateDTO extends DTO
{
    use ReplyTrait;

    public ?int $update_id = null;

    protected array $required = [
        'update_id',
    ];

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return null;
    }
}
