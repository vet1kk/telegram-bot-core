<?php

declare(strict_types=1);

namespace Bot\Listener;

use Bot\Attribute\Listener;
use Bot\Event\Events\UnhandledEvent;
use Bot\Http\ClientInterface;
use Bot\Http\Message\SendMessage;
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

        if (isset($chatId)) {
            try {
                $message = SendMessage::create()
                                      ->setChatId($chatId)
                                      ->setText(_("Sorry, I didn't understand that message."));
                $this->client->sendMessage($message);
            } catch (\Throwable $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
