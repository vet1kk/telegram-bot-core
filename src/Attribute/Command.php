<?php

declare(strict_types=1);

namespace Bot\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Command
{
    /**
     * @param string $name
     * @param string $description
     */
    public function __construct(public string $name, public string $description)
    {
    }
}
