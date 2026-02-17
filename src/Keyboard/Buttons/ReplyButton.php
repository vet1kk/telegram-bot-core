<?php

declare(strict_types=1);

namespace Bot\Keyboard\Buttons;

/**
 * @extends \Bot\Keyboard\Buttons\Button<\Bot\Keyboard\Buttons\ReplyButton>
 */
class ReplyButton extends Button
{
    protected bool $requestContact = false;
    protected bool $requestLocation = false;

    /**
     * @param bool $requestContact
     * @return self
     */
    public function setRequestContact(bool $requestContact): self
    {
        $this->requestContact = $requestContact;

        return $this;
    }

    /**
     * @param bool $requestLocation
     * @return self
     */
    public function setRequestLocation(bool $requestLocation): self
    {
        $this->requestLocation = $requestLocation;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $data = ['text' => $this->text];

        if ($this->requestContact) {
            $data['request_contact'] = true;
        }
        if ($this->requestLocation) {
            $data['request_location'] = true;
        }

        return $data;
    }
}
