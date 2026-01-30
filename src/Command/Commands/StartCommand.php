<?php

declare(strict_types=1);

namespace Bot\Command\Commands;

use Bot\Command\CommandInterface;
use Bot\Logger\Logger;
use Bot\Update;
use Psr\Log\LogLevel;

class StartCommand implements CommandInterface
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'start';
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
//            $this->client->sendMessage($update->getChatId(), 'Hello! 👋 Welcome to the bot.');
        } catch (\Throwable $e) {
            Logger::log(LogLevel::ERROR, $e->getMessage());
        }
    }
}
