<?php

declare(strict_types=1);

namespace Bot\Command\Commands;

use Bot\Attribute\Command;
use Bot\Command\CommandInterface;
use Bot\Http\Client;
use Bot\Update;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

#[Command(name: 'start', description: 'Initiate interaction with the bot')]
class StartCommand implements CommandInterface
{
    /**
     * @param Client $client
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(protected Client $client, protected LoggerInterface $logger)
    {
    }

    /**
     * @inheritDoc
     */
    public function handle(Update $update): void
    {
        $this->logger->log(LogLevel::INFO, 'Start command executed', [
            'chat_id' => $update->getChatId(),
        ]);
        try {
            $this->client->sendMessage($update->getChatId(), 'Hello! 👋 Welcome to the bot.');
        } catch (\Throwable $e) {
            $this->logger->log(LogLevel::ERROR, $e->getMessage());
        }
    }
}
