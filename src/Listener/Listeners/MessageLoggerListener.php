<?php

declare(strict_types=1);

namespace Bot\Listener\Listeners;

use Bot\Attribute\Listener;
use Bot\Event\Events\ReceivedEvent;
use Bot\Http\Client;
use Bot\Listener\ListenerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class MessageLoggerListener implements ListenerInterface
{
    public function __construct(protected Client $client, protected LoggerInterface $logger)
    {
    }

    #[Listener(eventClass: ReceivedEvent::class)]
    public function onMessageReceived(ReceivedEvent $event): void
    {
        $update = $event->getUpdate();

        $this->logger->log(LogLevel::INFO, 'Non-command message received', [
            'chat_id' => $update->getChatId(),
            'text' => $update->getText()
        ]);
    }
}
