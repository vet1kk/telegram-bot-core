<?php

declare(strict_types=1);

namespace Bot\DTO\Message;

use Bot\DTO\DTO;
use Bot\Enum\ForwardOriginType;

/**
 * @template T of \Bot\DTO\Message\ForwardOriginDTO
 * @extends \Bot\DTO\DTO<T>
 */
class ForwardOriginDTO extends DTO
{
    public ?string $type = null;
    public ?int $date = null;

    public ?UserDTO $sender_user = null;

    public ?string $sender_user_name = null;

    public ?ChatDTO $sender_chat = null;
    public ?string $author_signature = null;

    public ?ChatDTO $chat = null;
    public ?int $message_id = null;

    /**
     * @inheritDoc
     */
    public function validate(): void
    {
        $required = ['type', 'date'];

        if ($this->type) {
            $type = ForwardOriginType::tryFrom($this->type);

            match ($type) {
                ForwardOriginType::USER => $required[] = 'sender_user',
                ForwardOriginType::HIDDEN_USER => $required[] = 'sender_user_name',
                ForwardOriginType::CHAT => $required[] = 'sender_chat',
                ForwardOriginType::CHANNEL => array_push($required, 'chat', 'message_id'),
                default => null,
            };
        }

        $this->required = $required;

        parent::validate();
    }

    /**
     * @return bool
     */
    public function fromUser(): bool
    {
        return $this->type === ForwardOriginType::USER->value;
    }

    /**
     * @return bool
     */
    public function fromChannel(): bool
    {
        return $this->type === ForwardOriginType::CHANNEL->value;
    }
}
