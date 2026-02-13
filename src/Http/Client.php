<?php

declare(strict_types=1);

namespace Bot\Http;

use Bot\Http\Exception\TelegramException;
use GuzzleHttp\Client as HttpClient;

class Client
{
    protected HttpClient $client;

    /**
     * @param string $token
     * @param array $config
     */
    public function __construct(string $token, array $config = [])
    {
        $config['base_uri'] = "https://api.telegram.org/bot$token/";
        $config['timeout'] = $config['timeout'] ?? 10;

        $this->client = new HttpClient($config);
    }

    /**
     * @param string $method
     * @param array $params
     * @return array
     * @throws \Bot\Http\Exception\TelegramException
     */
    public function request(string $method, array $params = []): array
    {
        try {
            $response = $this->client->post($method, [
                'json' => $params,
            ]);

            return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            throw new TelegramException('HTTP request failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param ?int $chatId
     * @param string $text
     * @return array
     * @throws \Bot\Http\Exception\TelegramException
     */
    public function sendMessage(?int $chatId, string $text): array
    {
        return $this->request('sendMessage', [
            'chat_id' => $chatId,
            'text' => $text,
        ]);
    }

    /**
     * @param string $url
     * @return array
     * @throws \Bot\Http\Exception\TelegramException
     */
    public function setWebhook(string $url): array
    {
        return $this->request('setWebhook', [
            'url' => $url,
        ]);
    }
}
