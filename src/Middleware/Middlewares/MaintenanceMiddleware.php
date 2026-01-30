<?php

declare(strict_types=1);

namespace Bot\Middleware\Middlewares;

use Bot\Middleware\MiddlewareInterface;
use Bot\Update;

class MaintenanceMiddleware implements MiddlewareInterface
{
    public function __construct(protected bool $enabled = false)
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
        if ($this->enabled) {
//            $client->sendMessage($update->getChatId(), "🚧 We are currently down for maintenance!");

            return;
        }

        $next($update);
    }
}
