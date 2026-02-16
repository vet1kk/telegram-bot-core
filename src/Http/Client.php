<?php

declare(strict_types=1);

namespace Bot\Http;

use Bot\Http\Exception\TelegramException;
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
     * @param int|string|null $chatId
     * @param string $text
     * @param array|\JsonSerializable $replyMarkup
     * @param array $options
     * @return array
     * @throws \Bot\Http\Exception\TelegramException
     * @throws \JsonException
     */
    public function sendMessage(
        int|string|null $chatId,
        string $text,
        array|\JsonSerializable $replyMarkup = [],
        array $options = []
    ): array {
        $payload = [
            ...$options,
            'chat_id' => $chatId,
            'text' => $text,
        ];
        if (!empty($replyMarkup)) {
            $data = ($replyMarkup instanceof \JsonSerializable)
                ? $replyMarkup->jsonSerialize()
                : $replyMarkup;

            $payload['reply_markup'] = json_encode($data, JSON_THROW_ON_ERROR);
        }

        return $this->request('sendMessage', $payload);
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
