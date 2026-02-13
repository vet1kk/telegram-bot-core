<?php

declare(strict_types=1);

namespace Bot\Provider;

use DI\Container;

interface ServiceProviderInterface
{
    /**
     * @param \DI\Container $container
     * @return void
     */
    public function register(Container $container): void;
}
