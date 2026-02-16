<?php

declare(strict_types=1);

namespace Bot\DTO\Message;

use Bot\DTO\DTO;

/**
 * @extends \Bot\DTO\DTO<\Bot\DTO\Message\PhotoSizeDTO>
 */
class PhotoSizeDTO extends DTO
{
    public ?string $file_id = null;
    public ?string $file_unique_id = null;
    public ?int $width = null;
    public ?int $height = null;
    public ?int $file_size = null;

    protected array $required = [
        'file_id',
        'file_unique_id',
        'width',
        'height',
    ];
}
