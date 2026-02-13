<?php

declare(strict_types=1);

namespace Bot\Keyboard\Buttons;

class UrlButton extends Button
{
    protected ?string $url = null;

    /**
     * @param string $url
     * @return self
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'text' => $this->getText(),
            'url' => $this->url,
        ];
    }

    /**
     * @inheritDoc
     */
    public function isValid(): bool
    {
        return $this->url !== null;
    }
}
