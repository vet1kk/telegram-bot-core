<?php

declare(strict_types=1);

namespace Bot\Config;

use Bot\Trait\OptionsTrait;

class ConfigService implements ConfigServiceInterface
{
    use OptionsTrait;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }
}
