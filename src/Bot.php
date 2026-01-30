<?php

declare(strict_types=1);

namespace Bot;

use Bot\Command\CommandInterface;
use Bot\Command\CommandManager;
use Bot\Event\EventManager;
use Bot\Http\Client;
use Bot\Logger\Logger;
use Bot\Middleware\MiddlewareInterface;
use Bot\Middleware\MiddlewareManager;
use Bot\Receiver\ReceiverInterface;
use Bot\Routing\Router;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Bot
{
    protected Client $client;
    protected Router $router;
    protected CommandManager $commandManager;
    protected EventManager $eventManager;
    protected MiddlewareManager $middlewareManager;

    protected function __construct(string $token, array $options = [])
    {
        $this->client = new Client($token, $options);
        $this->commandManager = new CommandManager();
        $this->eventManager = new EventManager();
        $this->middlewareManager = new MiddlewareManager();
        $this->router = new Router($this->commandManager, $this->eventManager);
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
     * @param \Bot\Middleware\MiddlewareInterface $middleware
     * @return self
     */
    public function withMiddleware(MiddlewareInterface $middleware): self
    {
        $this->middlewareManager->register($middleware);

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
    public function run(Update $update): void
    {
        Logger::log(LogLevel::DEBUG, 'Incoming update', ['update' => $update]);

        $destination = function (Update $update) {
            $this->router->route($update);
        };

        $this->middlewareManager->process($update, $destination);
    }

    /**
     * @return void
     */
    public function runFromWebhook(): void
    {
        $input = file_get_contents('php://input');

        try {
            $update = json_decode($input, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            Logger::log(LogLevel::ERROR, 'Failed to decode webhook input', [
                'error' => $e->getMessage(),
                'input' => $input
            ]);

            return;
        }
        Logger::log(LogLevel::DEBUG, 'Received update from webhook', ['update' => $update]);

        if (!$update) {
            return;
        }

        $this->run(new Update($update));
    }

    /**
     * @param string $url
     * @return static
     * @throws \Bot\Http\Exception\TelegramException
     */
    public function registerWebhook(string $url): static
    {
        $this->client->setWebhook($url);

        return $this;
    }
}
