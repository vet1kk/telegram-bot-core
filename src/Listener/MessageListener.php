<?php

declare(strict_types=1);

namespace Bot\Listener;

use Bot\Attribute\Listener;
use Bot\Event\Events\UnhandledEvent;
use Psr\Log\LoggerInterface;

class MessageListener implements ListenerInterface
{
    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(protected LoggerInterface $logger)
    {
    }

    #[Listener(eventClass: UnhandledEvent::class)]
    public function onUnhandledEvent(UnhandledEvent $event): void
    {
        $this->logger->info('Received unhandled update', ['update' => $event->getUpdate()]);
        $update = $event->getUpdate();

        if ($update->getChatId() !== null) {
            try {
                $update->reply(_("Sorry, I didn't understand that message."));
            } catch (\Throwable $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
