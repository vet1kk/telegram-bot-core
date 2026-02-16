<?php

declare(strict_types=1);

namespace Bot\Listener;

use Bot\Attribute\Listener;
use Bot\Event\Events\UnhandledEvent;
use Bot\Http\ClientInterface;
use Psr\Log\LoggerInterface;

class MessageListener implements ListenerInterface
{
    /**
     * @param \Bot\Http\ClientInterface $client
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(protected ClientInterface $client, protected LoggerInterface $logger)
    {
    }

    #[Listener(eventClass: UnhandledEvent::class)]
    public function onUnhandledEvent(UnhandledEvent $event): void
    {
        $this->logger->info('Received unhandled update', ['update' => $event->getUpdate()]);
        $chatId = $event->getUpdate()->getChatId();

        if ($chatId !== null) {
            try {
                $this->client->sendMessage($chatId, "Sorry, I didn't understand that message.");
            } catch (\Throwable $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
