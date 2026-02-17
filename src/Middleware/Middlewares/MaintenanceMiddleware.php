<?php

declare(strict_types=1);

namespace Bot\Middleware\Middlewares;

use Bot\Config\ConfigServiceInterface;
use Bot\DTO\Update\UpdateDTO;
use Bot\Http\ClientInterface;
use Bot\Http\Message\SendMessage;
use Bot\Middleware\MiddlewareInterface;
use Psr\Log\LoggerInterface;

class MaintenanceMiddleware implements MiddlewareInterface
{
    /**
     * @param \Bot\Http\ClientInterface $client
     * @param \Bot\Config\ConfigServiceInterface $config
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        protected ClientInterface $client,
        protected ConfigServiceInterface $config,
        protected LoggerInterface $logger
    ) {
    }

    /**
     * @inheritDoc
     * @throws \Bot\Http\Exception\TelegramException
     */
    public function process(UpdateDTO $update, callable $next): void
    {
        if ($update->getChatId() !== null && !empty($this->config->getOption('maintenance.enabled'))) {
            try {
                $message = SendMessage::create()
                                      ->setChatId($update->getChatId())
                                      ->setText(_('🚧 We are currently down for maintenance!'));
                $this->client->sendMessage($message);
            } catch (\Throwable $e) {
                $this->logger->error($e->getMessage());
            }

            return;
        }

        $next($update);
    }
}
