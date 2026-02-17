<?php

declare(strict_types=1);

namespace Bot\Http;

use Bot\Http\Message\SendMessageInterface;

interface ClientInterface
{
    /**
     * @param string $method
     * @param array $params
     * @return array
     */
    public function request(string $method, array $params = []): array;

    /**
     * @param \Bot\Http\Message\SendMessageInterface $message
     * @return array
     */
    public function sendMessage(SendMessageInterface $message): array;

    /**
     * @param string $url
     * @return array
     */
    public function setWebhook(string $url): array;
}
