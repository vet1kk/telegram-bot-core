<?php

declare(strict_types=1);

namespace Bot\Routing;

use Bot\Command\CommandManager;
use Bot\Event\EventManager;
use Bot\Event\Events\CommandHandledEvent;
use Bot\Event\Events\ReceivedEvent;
use Bot\Logger\Logger;
use Bot\Receiver\ReceiverInterface;
use Bot\Update;
use Psr\Log\LogLevel;

class Router
{
    /**
     * @param \Bot\Command\CommandManager $commandManager
     * @param \Bot\Event\EventManager $eventManager
     */
    public function __construct(protected CommandManager $commandManager, protected EventManager $eventManager)
    {
    }

    /**
     * @var \Bot\Receiver\ReceiverInterface[]
     */
    protected array $receivers = [];

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
     */
    public function route(Update $update): void
    {
        foreach ($this->receivers as $receiver) {
            Logger::log(LogLevel::DEBUG, 'Receiver called', [
                'receiver' => $receiver::class
            ]);

            if ($receiver->supports($update)) {
                $command = $this->commandManager->resolve($update);
                if ($command) {
                    Logger::log(LogLevel::DEBUG, 'Executing command: ' . $command->getName());

                    $command->handle($update);

                    $this->eventManager->emit(new CommandHandledEvent($command, $update));

                    return;
                }
                $this->eventManager->emit(new ReceivedEvent($update));

                return;
            }
        }

        $this->eventManager->emit(new ReceivedEvent($update));
    }
}
