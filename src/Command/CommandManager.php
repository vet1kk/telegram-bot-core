<?php

declare(strict_types=1);

namespace Bot\Command;

use Bot\Attribute\Command as CommandAttr;
use Bot\DTO\Update\MessageUpdateDTO;
use Bot\Event\EventManagerInterface;
use Bot\Event\Events\CommandHandledEvent;
use Bot\Event\Events\UnhandledEvent;
use Bot\Trait\ServiceGetterTrait;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use ReflectionClass;

class CommandManager implements CommandManagerInterface
{
    use ServiceGetterTrait;

    /**
     * @var array<string, class-string<\Bot\Command\CommandInterface>>
     */
    protected array $commands = [];

    /**
     * @param \Psr\Container\ContainerInterface $container
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Bot\Event\EventManagerInterface $eventManager
     */
    public function __construct(
        protected ContainerInterface $container,
        protected LoggerInterface $logger,
        protected EventManagerInterface $eventManager
    ) {
    }

    /**
     * @return $this
     * @throws \ReflectionException
     */
    public function register(string $commandClass): self
    {
        if (!is_a($commandClass, CommandInterface::class, true)) {
            throw new \InvalidArgumentException("Class $commandClass must implement " . CommandInterface::class . '.');
        }

        $reflection = new ReflectionClass($commandClass);
        $attributes = $reflection->getAttributes(CommandAttr::class);

        if (empty($attributes)) {
            throw new \InvalidArgumentException("Class $commandClass is missing the Command attribute.");
        }

        if (count($attributes) > 1) {
            throw new \LogicException("Class $commandClass has multiple Command attributes. Only one is allowed.");
        }

        $attrInstance = current($attributes)->newInstance();
        $this->commands[$attrInstance->name] = $commandClass;

        return $this;
    }

    /**
     * @param \Bot\DTO\Update\MessageUpdateDTO $update
     * @return ?\Bot\Command\CommandInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function resolve(MessageUpdateDTO $update): ?CommandInterface
    {
        $text = $update->message?->text;
        if (!isset($text)) {
            return null;
        }

        if (!str_starts_with($text, '/')) {
            return null;
        }

        $name = explode(' ', ltrim($text, '/'))[0];
        /** @var class-string<\Bot\Command\CommandInterface>|null $class */
        $class = $this->commands[$name] ?? null;

        if ($class === null) {
            return null;
        }

        return $this->getService($class);
    }

    /**
     * @param \Bot\DTO\Update\MessageUpdateDTO $update
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function handle(MessageUpdateDTO $update): void
    {
        $command = $this->resolve($update);
        if ($command) {
            $this->logger->info('Executing command: ' . $command::class, [
                'update' => $update->jsonSerialize(),
            ]);

            $command->handle($update);

            $this->eventManager->emit(new CommandHandledEvent($command, $update));

            return;
        }
        $this->eventManager->emit(new UnhandledEvent($update));
    }

    /**
     * @return array<string, string>
     * @throws \ReflectionException
     */
    public function getCommands(): array
    {
        $commands = [];

        foreach ($this->commands as $commandClass) {
            $reflection = new \ReflectionClass($commandClass);
            $attributes = $reflection->getAttributes(CommandAttr::class);

            if (isset($attributes[0])) {
                /** @var CommandAttr $attr */
                $attr = $attributes[0]->newInstance();
                $commands[$attr->name] = $attr->description;
            }
        }

        return $commands;
    }

    /**
     * @return \Psr\Container\ContainerInterface
     */
    protected function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
