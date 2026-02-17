<?php

declare(strict_types=1);

namespace Bot\Http\Message;

use Bot\Keyboard\KeyboardInterface;

class SendMessage implements SendMessageInterface
{
    protected int|string|null $chatId = null;
    protected ?string $text = null;
    protected ?KeyboardInterface $keyboard = null;
    protected array $options = [];

    /**
     * @return self
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * @param int|string|null $chatId
     * @return self
     */
    public function setChatId(int|string|null $chatId): self
    {
        $this->chatId = $chatId;

        return $this;
    }

    /**
     * @param string $text
     * @return self
     */
    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @param \Bot\Keyboard\KeyboardInterface $keyboard
     * @return self
     */
    public function setKeyboard(KeyboardInterface $keyboard): self
    {
        $this->keyboard = $keyboard;

        return $this;
    }

    /**
     * @param array $options
     * @return self
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setOption(string $key, mixed $value): self
    {
        $this->options[$key] = $value;

        return $this;
    }

    /**
     * @throws \JsonException
     */
    public function jsonSerialize(): array
    {
        $this->validate();

        $payload = [
            ...$this->options,
            'chat_id' => $this->chatId,
            'text' => $this->text,
        ];

        if (!empty($this->keyboard)) {
            $keyboard = $this->keyboard->jsonSerialize();
            $payload['reply_markup'] = json_encode($keyboard, JSON_THROW_ON_ERROR);
        }

        return $payload;
    }

    /**
     * @return void
     * @throws \InvalidArgumentException
     */
    public function validate(): void
    {
        if ($this->chatId === null) {
            throw new \InvalidArgumentException('Chat ID is required');
        }
        if ($this->text === null) {
            throw new \InvalidArgumentException('Text is required');
        }
        if ($this->keyboard && !$this->keyboard->isValid()) {
            throw new \InvalidArgumentException('Invalid keyboard');
        }
    }
}
