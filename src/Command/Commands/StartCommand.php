<?php

declare(strict_types=1);

namespace Bot\Command\Commands;

use Bot\Attribute\Command;
use Bot\Command\CommandInterface;
use Bot\DTO\Update\MessageUpdateDTO;
use Bot\Http\ClientInterface;
use Bot\Http\Message\SendMessage;
use Psr\Log\LoggerInterface;

#[Command(name: 'start', description: 'Initiate interaction with the bot')]
class StartCommand implements CommandInterface
{
    /**
     * @param \Bot\Http\ClientInterface $client
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(protected ClientInterface $client, protected LoggerInterface $logger)
    {
    }

    /**
     * @inheritDoc
     */
    public function handle(MessageUpdateDTO $update): void
    {
        try {
            $message = SendMessage::create()
                                  ->setChatId($update->getChatId())
                                  ->setText(_('Hello! 👋 Welcome to the bot.'));
            $this->client->sendMessage($message);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
