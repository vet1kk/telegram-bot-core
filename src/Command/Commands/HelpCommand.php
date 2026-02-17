<?php

declare(strict_types=1);

namespace Bot\Command\Commands;

use Bot\Attribute\Command;
use Bot\Command\CommandInterface;
use Bot\Command\CommandManagerInterface;
use Bot\DTO\Update\MessageUpdateDTO;
use Bot\Http\ClientInterface;
use Bot\Http\Message\SendMessage;
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
            $message = SendMessage::create()
                                  ->setChatId($update->getChatId())
                                  ->setText(_('No commands are currently available.'));
            $this->client->sendMessage($message);

            return;
        }

        $lines = [];
        foreach ($commands as $name => $description) {
            $lines[] = sprintf("/%s - %s", $name, $description);
        }

        $text = "<b>🌟 " . _('Available Commands') . "</b>\n\n" . implode("\n", $lines);

        try {
            $message = SendMessage::create()
                                  ->setChatId($update->getChatId())
                                  ->setText($text)
                                  ->setOptions([
                                      'parse_mode' => 'HTML'
                                  ]);
            $this->client->sendMessage($message);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
