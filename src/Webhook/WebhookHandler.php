<?php

declare(strict_types=1);

namespace Bot\Webhook;

use Psr\Log\LoggerInterface;

class WebhookHandler implements WebhookHandlerInterface
{
    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(protected LoggerInterface $logger)
    {
    }

    /**
     * @inheritDoc
     */
    public function handle(): ?array
    {
        $input = file_get_contents('php://input');
        if ($input === false) {
            return null;
        }

        try {
            return json_decode($input, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $this->logger->error('Failed to decode webhook', ['error' => $e->getMessage()]);

            return null;
        }
    }
}
