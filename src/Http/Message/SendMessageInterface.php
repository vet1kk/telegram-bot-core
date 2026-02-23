<?php

declare(strict_types=1);

namespace Bot\Http\Message;

use Bot\Keyboard\KeyboardInterface;

interface SendMessageInterface extends \JsonSerializable
{
    /**
     * @param int|string|null $chatId
     * @return \Bot\Http\Message\SendMessageInterface
     */
    public function setChatId(int|string|null $chatId): self;

    /**
     * @param string|null $text
     * @return \Bot\Http\Message\SendMessageInterface
     */
    public function setText(?string $text): self;

    /**
     * @param \Bot\Keyboard\KeyboardInterface|null $keyboard
     * @return \Bot\Http\Message\SendMessageInterface
     */
    public function setKeyboard(?KeyboardInterface $keyboard): self;

    /**
     * @param array $options
     * @return \Bot\Http\Message\SendMessageInterface
     */
    public function setOptions(array $options): self;
}
