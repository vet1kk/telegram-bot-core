<?php

declare(strict_types=1);

namespace Bot\Command\Commands;

use Bot\Attribute\Command;
use Bot\Command\CommandInterface;
use Bot\Http\Client;
use Bot\Logger\Logger;
use Bot\Update;
use Psr\Log\LogLevel;

#[Command(name: 'start', description: 'Initiate interaction with the bot')]
class StartCommand implements CommandInterface
{
    /**
     * @param Client $client
     */
    public function __construct(protected Client $client)
    {
    }

    /**
     * @inheritDoc
     */
    public function handle(Update $update): void
    {
        Logger::log(LogLevel::DEBUG, 'Start command executed', [
            'chat_id' => $update->getChatId(),
        ]);
        try {
            $this->client->sendMessage($update->getChatId(), 'Hello! 👋 Welcome to the bot.');
        } catch (\Throwable $e) {
            Logger::log(LogLevel::ERROR, $e->getMessage());
        }
    }
}
