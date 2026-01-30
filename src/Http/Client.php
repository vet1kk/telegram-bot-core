<?php

declare(strict_types=1);

namespace Bot\Http;

use Bot\Http\Exception\TelegramException;
use GuzzleHttp\Client as HttpClient;

class Client
{
    protected static ?self $instance = null;
    protected static ?HttpClient $client = null;

    /**
     * @param string $token
     * @param array $config
     */
    private function __construct(string $token, array $config = [])
    {
        $config['base_uri'] = "https://api.telegram.org/bot$token/";
        $config['timeout'] = $config['timeout'] ?? 10;

        static::$client = new HttpClient($config);
    }

    /**
     * @param string $token
     * @param array $config
     */
    public static function init(string $token, array $config = []): void
    {
        self::$instance ??= new self($token, $config);
    }

    /**
     * @return HttpClient
     * @throws \Bot\Http\Exception\TelegramException
     */
    protected static function getClient(): HttpClient
    {
        if (!static::$client) {
            throw new TelegramException('Client not initialized.');
        }

        return static::$client;
    }

    /**
     * @param string $method
     * @param array $params
     * @return array
     * @throws \Bot\Http\Exception\TelegramException
     */
    public static function request(string $method, array $params = []): array
    {
        try {
            $response = static::getClient()->post($method, [
                'json' => $params,
            ]);

            return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            throw new TelegramException('HTTP request failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param int $chatId
     * @param string $text
     * @return array
     * @throws \Bot\Http\Exception\TelegramException
     */
    public static function sendMessage(int $chatId, string $text): array
    {
        return static::request('sendMessage', [
            'chat_id' => $chatId,
            'text' => $text,
        ]);
    }

    /**
     * @param string $url
     * @return array
     * @throws \Bot\Http\Exception\TelegramException
     */
    public static function setWebhook(string $url): array
    {
        return static::request('setWebhook', [
            'url' => $url,
        ]);
    }
}
