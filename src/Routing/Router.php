<?php

declare(strict_types=1);

namespace Bot\Routing;

use Bot\Command\CommandManager;
use Bot\Event\EventManager;
use Bot\Event\Events\CommandHandledEvent;
use Bot\Event\Events\ReceivedEvent;
use Bot\Event\Events\UnhandledEvent;
use Bot\Receiver\ReceiverInterface;
use Bot\Update;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Router
{
    /**
     * @var \Bot\Receiver\ReceiverInterface[]
     */
    protected array $receivers = [];

    /**
     * @param \Bot\Command\CommandManager $commandManager
     * @param \Bot\Event\EventManager $eventManager
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        protected CommandManager $commandManager,
        protected EventManager $eventManager,
        protected LoggerInterface $logger
    ) {
    }

    /**
     * @param \Bot\Receiver\ReceiverInterface $receiver
     * @return self
     */
    public function addReceiver(ReceiverInterface $receiver): self
    {
        $this->receivers[] = $receiver;

        return $this;
    }

    /**
     * @param \Bot\Update $update
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function route(Update $update): void
    {
        foreach ($this->receivers as $receiver) {
            $this->logger->log(LogLevel::INFO, 'Receiver called', [
                'receiver' => $receiver::class
            ]);

            if ($receiver->supports($update)) {
                $command = $this->commandManager->resolve($update);
                if ($command) {
                    $this->logger->log(LogLevel::INFO, 'Executing command: ' . $command::class, [
                        'chat_id' => $update->getChatId(),
                    ]);

                    $command->handle($update);

                    $this->eventManager->emit(new CommandHandledEvent($command, $update));

                    return;
                }
                $this->eventManager->emit(new ReceivedEvent($update));

                return;
            }
        }

        $this->eventManager->emit(new UnhandledEvent($update));
    }
}
