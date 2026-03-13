<?php

declare(strict_types=1);

namespace Bot\DTO\Message;

use Bot\DTO\DTO;

class DocumentDTO extends DTO
{
    public ?string $file_id = null;
    public ?string $file_unique_id = null;
    public ?string $file_name = null;
    public ?string $mime_type = null;
    public ?int $file_size = null;
    public ?PhotoSizeDTO $thumb = null;
    public ?PhotoSizeDTO $thumbnail = null;

    /** @var list<string> */
    protected array $required = [
        'file_id',
        'file_unique_id',
    ];
}
