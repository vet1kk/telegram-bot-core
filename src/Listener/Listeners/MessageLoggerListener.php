<?php

declare(strict_types=1);

namespace Bot\Listener\Listeners;

use Bot\Attribute\Listener;
use Bot\Event\Events\UnhandledEvent;
use Bot\Http\Client;
use Bot\Listener\ListenerInterface;
use Psr\Log\LoggerInterface;

class MessageLoggerListener implements ListenerInterface
{
    /**
     * @param \Bot\Http\Client $client
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(protected Client $client, protected LoggerInterface $logger)
    {
    }

    #[Listener(eventClass: UnhandledEvent::class)]
    public function onUnhandledEvent(UnhandledEvent $event): void
    {
        $this->logger->info('Received unhandled update', ['update' => $event->getUpdate()]);
    }
}
