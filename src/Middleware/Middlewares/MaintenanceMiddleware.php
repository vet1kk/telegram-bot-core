<?php

declare(strict_types=1);

namespace Bot\Middleware\Middlewares;

use Bot\ConfigService;
use Bot\Http\Client;
use Bot\Middleware\MiddlewareInterface;
use Bot\Update;

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
     * @param \Bot\Update $update
     * @param callable $next
     * @return void
     * @throws \Bot\Http\Exception\TelegramException
     */
    public function process(Update $update, callable $next): void
    {
        if (!empty($this->config->get('maintenance.enabled'))) {
            $this->client->sendMessage($update->getChatId(), "🚧 We are currently down for maintenance!");

            return;
        }

        $next($update);
    }
}
