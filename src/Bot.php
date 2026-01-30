<?php

declare(strict_types=1);

namespace Bot;

use Bot\Attribute\Listener;
use Bot\Command\CommandManager;
use Bot\Container\Container;
use Bot\Event\EventManager;
use Bot\Http\Client;
use Bot\Logger\Logger;
use Bot\Middleware\MiddlewareManager;
use Bot\Receiver\ReceiverInterface;
use Bot\Routing\Router;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Bot
{
    protected Container $container;
    protected Router $router;
    protected CommandManager $commandManager;
    protected EventManager $eventManager;
    protected MiddlewareManager $middlewareManager;

    protected function __construct(string $token, array $options = [])
    {
        $this->container = new Container();

        $client = new Client($token, $options);
        $this->container->set(Client::class, $client);

        $config = new ConfigService($options['config'] ?? []);
        $this->container->set(ConfigService::class, $config);

        $this->commandManager = new CommandManager($this->container);
        $this->eventManager = new EventManager($this->container);
        $this->router = new Router($this->commandManager, $this->eventManager);
        $this->middlewareManager = new MiddlewareManager($this->container);
    }

    /**
     * @param string $token
     * @param array $options
     * @return static
     */
    public static function create(string $token, array $options = []): static
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
     * @param string $middleware
     * @return self
     */
    public function withMiddleware(string $middleware): self
    {
        $this->middlewareManager->register($middleware);

        return $this;
    }

    /**
     * @param string $commandClass
     * @return self
     * @throws \ReflectionException
     */
    public function withCommand(string $commandClass): self
    {
        $this->commandManager->register($commandClass);

        return $this;
    }

    /**
     * @param string $listenerClass
     * @return self
     * @throws \ReflectionException
     */
    public function withListener(string $listenerClass): self
    {
        $reflection = new \ReflectionClass($listenerClass);

        foreach ($reflection->getMethods() as $method) {
            $attributes = $method->getAttributes(Listener::class);

            foreach ($attributes as $attribute) {
                /** @var \Bot\Attribute\Listener $instance */
                $instance = $attribute->newInstance();

                $this->eventManager->listen($instance->eventClass, $listenerClass, $method->getName());
            }
        }

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
     * @throws \Exception
     */
    public function registerWebhook(string $url): static
    {
        $this->container->get(Client::class)?->setWebhook($url);

        return $this;
    }
}
