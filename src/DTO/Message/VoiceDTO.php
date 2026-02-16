<?php

declare(strict_types=1);

namespace Bot\DTO\Message;

use Bot\DTO\DTO;

/**
 * @extends \Bot\DTO\DTO<\Bot\DTO\Message\VoiceDTO>
 */
class VoiceDTO extends DTO
{
    public ?string $file_id = null;
    public ?string $file_unique_id = null;
    public ?int $file_size = null;
    public ?int $duration = null;
    public ?string $mime_type = null;

    protected array $required = [
        'file_id',
        'file_unique_id',
        'duration',
    ];
}
