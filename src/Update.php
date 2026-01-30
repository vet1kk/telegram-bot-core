<?php

declare(strict_types=1);

namespace Bot;

class Update
{
    public const string TYPE_MESSAGE = 'message';
    public const string TYPE_EDITED_MESSAGE = 'edited_message';
    public const string TYPE_CALLBACK_QUERY = 'callback_query';
    public const string TYPE_INLINE_QUERY = 'inline_query';
    public const string TYPE_CHOSEN_INLINE_RESULT = 'chosen_inline_result';
    public const array TYPES = [
        self::TYPE_MESSAGE,
        self::TYPE_EDITED_MESSAGE,
        self::TYPE_CALLBACK_QUERY,
        self::TYPE_INLINE_QUERY,
        self::TYPE_CHOSEN_INLINE_RESULT,
    ];

    public function __construct(protected array $data)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function raw(): array
    {
        return $this->data;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getMessage(): ?array
    {
        return $this->data['message'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->getMessage()['text'] ?? null;
    }

    /**
     * @return int|null
     */
    public function getChatId(): ?int
    {
        return $this->getMessage()['chat']['id'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        foreach (self::TYPES as $type) {
            if (!empty($this->data[$type])) {
                return $type;
            }
        }

        return null;
    }
}
