<?php

declare(strict_types=1);

namespace Bot\DTO\Update;

use Bot\DTO\DTO;
use Bot\Trait\ReplyTrait;

class UpdateDTO extends DTO
{
    use ReplyTrait;

    public ?int $update_id = null;

    /** @var list<string> */
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
