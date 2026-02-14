<?php

declare(strict_types=1);

namespace Bot\Command;

use Bot\Attribute\Command as CommandAttr;
use Bot\DTO\Update\MessageUpdateDTO;
use Bot\Event\EventManager;
use Bot\Event\Events\CommandHandledEvent;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use ReflectionClass;

class CommandManager
{
    /**
     * @var array<string, class-string<\Bot\Command\CommandInterface>>
     */
    protected array $commands = [];

    /**
     * @param \Psr\Container\ContainerInterface $container
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Bot\Event\EventManager $eventManager
     */
    public function __construct(
        protected ContainerInterface $container,
        protected LoggerInterface $logger,
        protected EventManager $eventManager
    ) {
    }

    /**
     * @param class-string<\Bot\Command\CommandInterface> $commandClass
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
     * @param \Bot\DTO\Update\MessageUpdateDTO $update
     * @return ?\Bot\Command\CommandInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function resolve(MessageUpdateDTO $update): ?CommandInterface
    {
        if (!str_starts_with($update->message?->text, '/')) {
            return null;
        }

        $name = explode(' ', ltrim($update->message?->text, '/'))[0];
        $class = $this->commands[$name] ?? null;

        if (!$class) {
            return null;
        }

        return $this->container->get($class);
    }

    /**
     * @param \Bot\DTO\Update\MessageUpdateDTO $update
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
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
        }
    }
}
