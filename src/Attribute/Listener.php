<?php

declare(strict_types=1);

namespace Bot\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Listener
{
    /**
     * @param string $eventClass
     */
    public function __construct(public string $eventClass)
    {
    }
}
