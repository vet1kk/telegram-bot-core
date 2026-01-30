<?php

declare(strict_types=1);

namespace Bot\Command;

use Bot\Attribute\Command as CommandAttr;
use Bot\Update;
use Psr\Container\ContainerInterface;
use ReflectionClass;

class CommandManager
{
    /**
     * @var array<string, class-string<\Bot\Command\CommandInterface>>
     */
    protected array $commands = [];

    /**
     * @param \Psr\Container\ContainerInterface $container
     */
    public function __construct(protected ContainerInterface $container)
    {
    }

    /**
     * @param string $commandClass
     * @return void
     * @throws \ReflectionException
     */
    public function register(string $commandClass): void
    {
        $reflection = new ReflectionClass($commandClass);
        $attributes = $reflection->getAttributes(CommandAttr::class);

        if (empty($attributes)) {
            throw new \InvalidArgumentException("Class $commandClass is missing the Command attribute.");
        }

        $attrInstance = current($attributes)->newInstance();
        $this->commands[$attrInstance->name] = $commandClass;
    }

    /**
     * @param \Bot\Update $update
     * @return ?\Bot\Command\CommandInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function resolve(Update $update): ?CommandInterface
    {
        if ($update->getType() !== Update::TYPE_MESSAGE || !$update->getText()) {
            return null;
        }

        if (!str_starts_with($update->getText(), '/')) {
            return null;
        }

        $name = explode(' ', ltrim($update->getText(), '/'))[0];
        $class = $this->commands[$name] ?? null;

        if (!$class) {
            return null;
        }

        return $this->container->get($class);
    }
}
