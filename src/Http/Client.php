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
     * @param \Bot\Http\Message\SendMessageInterface $message
     * @return array
     * @throws \Bot\Http\Exception\TelegramException
     */
    public function sendMessage(SendMessageInterface $message): array
    {
        return $this->request('sendMessage', $message->jsonSerialize());
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
