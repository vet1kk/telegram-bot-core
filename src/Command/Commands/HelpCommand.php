<?php

declare(strict_types=1);

namespace Bot\Command\Commands;

use Bot\Attribute\Command;
use Bot\Command\CommandInterface;
use Bot\Command\CommandManagerInterface;
use Bot\DTO\Update\MessageUpdateDTO;
use Bot\Http\ClientInterface;
use Psr\Log\LoggerInterface;

#[Command(name: 'help', description: 'Get a list of available commands')]
class HelpCommand implements CommandInterface
{
    /**
     * @param \Bot\Http\ClientInterface $client
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Bot\Command\CommandManagerInterface $commandManager
     */
    public function __construct(
        protected ClientInterface $client,
        protected LoggerInterface $logger,
        protected CommandManagerInterface $commandManager
    ) {
    }

    /**
     * @inheritDoc
     */
    public function handle(MessageUpdateDTO $update): void
    {
        $commands = $this->commandManager->getCommands();

        if (empty($commands)) {
            $this->client->sendMessage($update->getChatId(), "No commands are currently available.");

            return;
        }

        $lines = [];
        foreach ($commands as $name => $description) {
            $lines[] = sprintf("/%s - %s", $name, $description);
        }

        $text = "<b>🌟 Available Commands</b>\n\n" . implode("\n", $lines);

        try {
            $this->client->sendMessage($update->getChatId(), $text, options: [
                'parse_mode' => 'HTML'
            ]);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
