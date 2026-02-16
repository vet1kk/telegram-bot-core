<?php

declare(strict_types=1);

namespace Bot\Http;

interface ClientInterface
{
    /**
     * @param string $method
     * @param array $params
     * @return array
     */
    public function request(string $method, array $params = []): array;

    /**
     * @param ?int $chatId
     * @param string $text
     * @param array|\JsonSerializable $replyMarkup
     * @param array $options
     * @return array
     */
    public function sendMessage(
        ?int $chatId,
        string $text,
        array|\JsonSerializable $replyMarkup = [],
        array $options = []
    ): array;

    /**
     * @param string $url
     * @return array
     */
    public function setWebhook(string $url): array;
}
