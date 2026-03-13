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
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'text' => $this->text,
            'url' => $this->url,
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getRequiredFields(): array
    {
        return array_merge(parent::getRequiredFields(), ['url']);
    }
}
