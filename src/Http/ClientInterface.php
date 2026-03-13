<?php

declare(strict_types=1);

namespace Bot\Http;

use Bot\Http\Message\SendMessageInterface;

interface ClientInterface
{
    /**
     * @param string $method
     * @param array<array-key, mixed> $params
     * @return array<array-key, mixed>
     */
    public function request(string $method, array $params = []): array;

    /**
     * @param \Bot\Http\Message\SendMessageInterface $message
     * @return array<array-key, mixed>
     */
    public function sendMessage(SendMessageInterface $message): array;

    /**
     * @param string $url
     * @return array<array-key, mixed>
     */
    public function setWebhook(string $url): array;
}
