<?php

declare(strict_types=1);

namespace Bot\Trait;

use Psr\Container\ContainerInterface;

trait ServiceGetterTrait
{
    /**
     * @return ContainerInterface
     */
    abstract protected function getContainer(): ContainerInterface;

    /**
     * @template T of object
     * @param class-string<T> $id
     * @return T
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \DI\DependencyException
     */
    protected function getService(string $id): object
    {
        /** @var T $service */
        $service = $this->getContainer()->get($id);

        return $service;
    }
}
