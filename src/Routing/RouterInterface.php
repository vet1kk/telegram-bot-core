<?php

declare(strict_types=1);

namespace Bot\Routing;

use Bot\DTO\Update\UpdateDTO;

interface RouterInterface
{
    /**
     * @param \Bot\DTO\Update\UpdateDTO $update
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function route(UpdateDTO $update): void;
}
