<?php

declare(strict_types=1);

namespace Bot\Command\Commands;

use Bot\Attribute\Command;
use Bot\Command\CommandInterface;
use Bot\DTO\Update\MessageUpdateDTO;
use Bot\Http\ClientInterface;
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
            $this->client->sendMessage($update->getChatId(), 'Hello! 👋 Welcome to the bot.');
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
