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
        $input = $this->readInput();
        if ($input === false) {
            return null;
        }

        return $this->handleRaw($input);
    }

    /**
     * Parse raw webhook payload.
     *
     * @return array<array-key, mixed>|null
     */
    public function handleRaw(string $input): ?array
    {
        try {
            $payload = json_decode($input, true, 512, JSON_THROW_ON_ERROR);

            if (!is_array($payload)) {
                throw new \JsonException('Webhook payload must decode to an array');
            }

            return $payload;
        } catch (\JsonException $e) {
            $this->logger->error('Failed to decode webhook', ['error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Read raw input from php://input.
     *
     * @return string|false
     */
    protected function readInput(): string|false
    {
        return file_get_contents('php://input');
    }
}
