<?php

declare(strict_types=1);

namespace Bot\Command\Commands;

use Bot\Attribute\Command;
use Bot\Command\CommandInterface;
use Bot\Command\CommandManagerInterface;
use Bot\DTO\Update\MessageUpdateDTO;
use Psr\Log\LoggerInterface;

#[Command(name: 'help', description: 'Get a list of available commands')]
class HelpCommand implements CommandInterface
{
    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Bot\Command\CommandManagerInterface $commandManager
     */
    public function __construct(
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

        try {
            if (empty($commands)) {
                $update->reply(_('No commands are currently available.'));

                return;
            }

            $lines = [];
            foreach ($commands as $name => $description) {
                $lines[] = sprintf("/%s - %s", $name, $description);
            }

            $text = "<b>🌟 " . _('Available Commands') . "</b>\n\n" . implode("\n", $lines);

            $update->reply($text, options: ['parse_mode' => 'HTML']);
        } catch (\Throwable $e) {
            $this->logger->error("Failed to send help message: " . $e->getMessage());
        }
    }
}
