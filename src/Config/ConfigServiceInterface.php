<?php

declare(strict_types=1);

namespace Bot\Config;

interface ConfigServiceInterface
{
    /**
     * @return array
     */
    public function getOptions(): array;

    /**
     * @param array $options
     * @param bool $override
     * @return self
     */
    public function setOptions(array $options = [], bool $override = false): self;

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function getOption(string $key, mixed $default = null): mixed;

    /**
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setOption(string $key, mixed $value): self;

    /**
     * @param string $key
     * @return self
     */
    public function unsetOption(string $key): self;
}
