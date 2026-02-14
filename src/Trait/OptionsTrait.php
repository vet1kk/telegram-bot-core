<?php

declare(strict_types=1);

namespace Bot\Trait;

trait OptionsTrait
{
    protected array $options = [];

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @param bool $override
     * @return self
     */
    public function setOptions(array $options = [], bool $override = false): self
    {
        if ($override) {
            $this->options = $options;
        } else {
            $this->options = array_merge($this->options, $options);
        }

        return $this;
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function getOption(string $key, mixed $default = null): mixed
    {
        return $this->options[$key] ?? $default;
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
     * @param string $key
     * @return self
     */
    public function unsetOption(string $key): self
    {
        unset($this->options[$key]);

        return $this;
    }
}
