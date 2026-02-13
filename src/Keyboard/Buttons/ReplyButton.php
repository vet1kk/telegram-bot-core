<?php

declare(strict_types=1);

namespace Bot\Keyboard\Buttons;

class ReplyButton extends Button
{
    protected bool $requestContact = false;
    protected bool $requestLocation = false;

    /**
     * @return self
     */
    public function requestContact(): self
    {
        $this->requestContact = true;

        return $this;
    }

    /**
     * @return self
     */
    public function requestLocation(): self
    {
        $this->requestLocation = true;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $data = ['text' => $this->getText()];

        if ($this->requestContact) {
            $data['request_contact'] = true;
        }
        if ($this->requestLocation) {
            $data['request_location'] = true;
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function isValid(): bool
    {
        return $this->requestContact || $this->requestLocation;
    }
}
