<?php

declare(strict_types=1);

namespace Bot;

use Bot\Command\CommandInterface;
use Bot\Command\CommandManager;
use Bot\Event\EventManager;
use Bot\Http\Client;
use Bot\Logger\Logger;
use Bot\Receiver\ReceiverInterface;
use Bot\Routing\Router;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Bot
{
    protected Router $router;
    protected CommandManager $commandManager;
    protected EventManager $eventManager;

    protected function __construct(string $token, array $options = [])
    {
        Client::init($token, $options);

        $this->router = Router::create();
        $this->commandManager = CommandManager::create();
        $this->eventManager = EventManager::create();
    }

    /**
     * @param string $token
     * @param array $options
     * @return static
     */
    public static function create(string $token, array $options): static
    {
        return new static($token, $options);
    }

    /**
     * @param \Psr\Log\LoggerInterface|null $logger
     * @return self
     */
    public function withLogger(?LoggerInterface $logger): self
    {
        Logger::setLogger($logger);

        return $this;
    }

    /**
     * @param \Bot\Receiver\ReceiverInterface $receiver
     * @return self
     */
    public function withReceiver(ReceiverInterface $receiver): self
    {
        $this->router->addReceiver($receiver);

        return $this;
    }

    /**
     * @param \Bot\Command\CommandInterface $command
     * @return self
     */
    public function withCommand(CommandInterface $command): self
    {
        $this->commandManager->register($command);

        return $this;
    }

    /**
     * @param string $eventClass
     * @param callable $handler
     * @return self
     */
    public function on(string $eventClass, callable $handler): self
    {
        $this->eventManager->on($eventClass, $handler);

        return $this;
    }

    /**
     * @param \Bot\Update $update
     * @return void
     */
    public static function run(Update $update): void
    {
        Logger::log(LogLevel::DEBUG, 'Incoming update', ['update' => $update]);
        Router::route($update);
    }

    /**
     * @return void
     * @throws \JsonException
     */
    public static function runFromWebhook(): void
    {
        $input = file_get_contents('php://input');

        $update = json_decode($input, true, 512, JSON_THROW_ON_ERROR);
        Logger::log(LogLevel::DEBUG, 'Received update from webhook', ['update' => $update]);

        if (!$update) {
            return;
        }

        static::run(new Update($update));
    }

    /**
     * @param string $url
     * @return static
     * @throws \Bot\Http\Exception\TelegramException
     */
    public function registerWebhook(string $url): static
    {
        Client::setWebhook($url);

        return $this;
    }
}
