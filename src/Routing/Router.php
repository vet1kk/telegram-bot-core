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
     * @var \Bot\Receiver\ReceiverInterface[]
     */
    protected static array $receivers = [];

    /**
     * @return self
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * @param \Bot\Receiver\ReceiverInterface $receiver
     * @return self
     */
    public function addReceiver(ReceiverInterface $receiver): self
    {
        static::$receivers[] = $receiver;

        return $this;
    }

    /**
     * @param \Bot\Update $update
     * @return void
     */
    public static function route(Update $update): void
    {
        foreach (static::$receivers as $receiver) {
            Logger::log(LogLevel::DEBUG, 'Receiver called', [
                'receiver' => $receiver::class
            ]);

            if ($receiver->supports($update)) {
                $command = CommandManager::resolve($update);
                if ($command) {
                    Logger::log(LogLevel::DEBUG, 'Executing command: ' . $command->getName());

                    $command->handle($update);

                    EventManager::emit(new CommandHandledEvent($command, $update));

                    return;
                }
                EventManager::emit(new ReceivedEvent($update));

                return;
            }
        }

        EventManager::emit(new ReceivedEvent($update));
    }
}
