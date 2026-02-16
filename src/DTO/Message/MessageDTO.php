<?php

declare(strict_types=1);

namespace Bot\DTO\Message;

use Bot\DTO\DTO;
use Bot\Enum\MessageMediaType;

/**
 * @extends \Bot\DTO\DTO<\Bot\DTO\Message\MessageDTO>
 */
class MessageDTO extends DTO
{
    public ?int $message_id = null;
    public ?ChatDTO $chat = null;
    public ?string $text = null;
    public ?UserDTO $from = null;
    public ?int $date = null;
    public ?int $edit_date = null;
    public ?ContactDTO $contact = null;
    public ?LocationDTO $location = null;
    public ?DocumentDTO $document = null;
    public ?VoiceDTO $voice = null;
    public ?VideoDTO $video = null;
    public ?VideoNoteDTO $video_note = null;
    public ?UserDTO $forward_from = null;
    public ?int $forward_date = null;
    public ?ForwardOriginDTO $forward_origin = null;

    /**
     * @var PhotoSizeDTO[]|null
     */
    public ?array $photo = null;

    protected array $required = [
        'message_id',
        'chat',
        'date',
    ];

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data = [], bool $validate = true): static
    {
        if (isset($data['photo']) && is_array($data['photo'])) {
            $data['photo'] = array_map(
                static fn(mixed $item) => PhotoSizeDTO::fromArray((array)$item, $validate),
                $data['photo']
            );
        }

        /** @var \Bot\DTO\Message\MessageDTO $self */
        $self = parent::fromArray($data, $validate);

        return $self;
    }

    /**
     * @return MessageMediaType|null
     */
    public function getMediaType(): ?MessageMediaType
    {
        foreach (MessageMediaType::cases() as $mediaType) {
            if (!empty($this->{$mediaType->value})) {
                return $mediaType;
            }
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isMedia(): bool
    {
        return $this->getMediaType() !== null;
    }
}
