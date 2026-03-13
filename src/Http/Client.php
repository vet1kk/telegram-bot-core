<?php

declare(strict_types=1);

namespace Bot\Http;

use Bot\Http\Exception\TelegramException;
use Bot\Http\Message\SendMessageInterface;
use GuzzleHttp\Client as HttpClient;

class Client implements ClientInterface
{
    protected HttpClient $client;

    /**
     * @param string $token
     * @param array<array-key, mixed> $config
     * @param \GuzzleHttp\Client|null $httpClient
     */
    public function __construct(string $token, array $config = [], ?HttpClient $httpClient = null)
    {
        if ($httpClient !== null) {
            $this->client = $httpClient;

            return;
        }

        $config['base_uri'] = "https://api.telegram.org/bot$token/";
        $config += ['timeout' => 10];

        $this->client = new HttpClient($config);
    }

    /**
     * @param string $method
     * @param array<array-key, mixed> $params
     * @return array<array-key, mixed>
     * @throws \Bot\Http\Exception\TelegramException
     */
    public function request(string $method, array $params = []): array
    {
        try {
            $response = $this->client->post($method, [
                'json' => $params,
            ]);

            $payload = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            if (!is_array($payload)) {
                throw new TelegramException('HTTP response payload must decode to an array');
            }

            return $payload;
        } catch (\Throwable $e) {
            throw new TelegramException('HTTP request failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param \Bot\Http\Message\SendMessageInterface $message
     * @return array<array-key, mixed>
     * @throws \Bot\Http\Exception\TelegramException
     */
    public function sendMessage(SendMessageInterface $message): array
    {
        $payload = $message->jsonSerialize();

        if (!is_array($payload)) {
            throw new TelegramException('Send message payload must be an array');
        }

        return $this->request('sendMessage', $payload);
    }

    /**
     * @param string $url
     * @return array<array-key, mixed>
     * @throws \Bot\Http\Exception\TelegramException
     */
    public function setWebhook(string $url): array
    {
        return $this->request('setWebhook', [
            'url' => $url,
        ]);
    }
}
