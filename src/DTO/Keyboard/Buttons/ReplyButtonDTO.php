<?php

declare(strict_types=1);

namespace Bot\DTO\Keyboard\Buttons;

/**
 * @extends \Bot\DTO\Keyboard\Buttons\ButtonDTO<\Bot\DTO\Keyboard\Buttons\ReplyButtonDTO
 */
class ReplyButtonDTO extends ButtonDTO
{
    public bool $request_contact = false;
    public bool $request_location = false;

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $data = ['text' => $this->text];

        if ($this->request_contact) {
            $data['request_contact'] = true;
        }
        if ($this->request_location) {
            $data['request_location'] = true;
        }

        return $data;
    }
}
