<?php

declare(strict_types=1);

namespace Bot\Webhook;

interface WebhookHandlerInterface
{
    /**
     * @return array|null
     */
    public function handle(): ?array;
}
