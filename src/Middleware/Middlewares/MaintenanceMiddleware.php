<?php

declare(strict_types=1);

namespace Bot\Middleware\Middlewares;

use Bot\ConfigService;
use Bot\DTO\Update\UpdateDTO;
use Bot\Http\Client;
use Bot\Middleware\MiddlewareInterface;

class MaintenanceMiddleware implements MiddlewareInterface
{
    /**
     * @param \Bot\Http\Client $client
     * @param \Bot\ConfigService $config
     */
    public function __construct(protected Client $client, protected ConfigService $config)
    {
    }

    /**
     * @inheritDoc
     * @throws \Bot\Http\Exception\TelegramException
     */
    public function process(UpdateDTO $update, callable $next): void
    {
        if ($update->getChatId() !== null && !empty($this->config->get('maintenance.enabled'))) {
            $this->client->sendMessage($update->getChatId(), "🚧 We are currently down for maintenance!");

            return;
        }

        $next($update);
    }
}
