<?php

declare(strict_types=1);

namespace Bot;

use Bot\Attribute\Listener;
use Bot\Command\CommandManager;
use Bot\Event\EventManager;
use Bot\Http\Client;
use Bot\Middleware\MiddlewareInterface;
use Bot\Middleware\MiddlewareManager;
use Bot\Provider\CoreServiceProvider;
use Bot\Provider\ServiceProviderInterface;
use Bot\Receiver\ReceiverInterface;
use Bot\Routing\Router;
use DI\Container;
use DI\ContainerBuilder;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Bot
{
    protected Container $container;

    /**
     * @throws \Exception
     */
    protected function __construct(protected string $token, protected array $options = [])
    {
        $builder = new ContainerBuilder();
        $this->container = $builder->build();

        $this->withServiceProvider(new CoreServiceProvider($token, $options));
    }

    /**
     * @param string $token
     * @param array $options
     * @return self
     * @throws \Exception
     */
    public static function create(string $token, array $options): self
    {
        return new self($token, $options);
    }

    /**
     * @return \DI\Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @param \Bot\Receiver\ReceiverInterface $receiver
     * @return self
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function withReceiver(ReceiverInterface $receiver): self
    {
        $this->container->get(Router::class)
                        ->addReceiver($receiver);

        return $this;
    }

    /**
     * @param \Bot\Provider\ServiceProviderInterface $provider
     * @return $this
     */
    public function withServiceProvider(ServiceProviderInterface $provider): self
    {
        $provider->register($this->container);

        return $this;
    }

    /**
     * @param class-string<MiddlewareInterface> $middleware
     * @return self
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function withMiddleware(string $middleware): self
    {
        $this->container->get(MiddlewareManager::class)
                        ->register($middleware);

        return $this;
    }

    /**
     * @param class-string<\Bot\Command\CommandInterface> $commandClass
     * @return self
     * @throws \ReflectionException|\Psr\Container\ContainerExceptionInterface
     */
    public function withCommand(string $commandClass): self
    {
        $this->container->get(CommandManager::class)
                        ->register($commandClass);

        return $this;
    }

    /**
     * @param class-string<\Bot\Listener\ListenerInterface> $listenerClass
     * @return self
     * @throws \ReflectionException|\Psr\Container\ContainerExceptionInterface
     */
    public function withListener(string $listenerClass): self
    {
        $reflection = new \ReflectionClass($listenerClass);

        foreach ($reflection->getMethods() as $method) {
            $attributes = $method->getAttributes(Listener::class);

            foreach ($attributes as $attribute) {
                /** @var \Bot\Attribute\Listener $instance */
                $instance = $attribute->newInstance();

                $this->container->get(EventManager::class)
                                ->listen($instance->eventClass, $listenerClass, $method->getName());
            }
        }

        return $this;
    }

    /**
     * @param \Bot\Update $update
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function run(Update $update): void
    {
        $this->container->get(LoggerInterface::class)
                        ->log(LogLevel::INFO, 'Incoming update', ['update' => $update]);

        $destination = function (Update $update): void {
            $this->container->get(Router::class)
                            ->route($update);
        };

        $this->container->get(MiddlewareManager::class)
                        ->process($update, $destination);
    }

    /**
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function runFromWebhook(): void
    {
        $input = file_get_contents('php://input');
        $logger = $this->container->get(LoggerInterface::class);

        try {
            $update = json_decode($input, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $logger->log(LogLevel::ERROR, 'Failed to decode webhook input', [
                'error' => $e->getMessage(),
                'input' => $input
            ]);

            return;
        }
        $logger->log(LogLevel::INFO, 'Received update from webhook', ['update' => $update]);

        if (!$update) {
            return;
        }

        $this->run(new Update($update));
    }

    /**
     * @param string $url
     * @return static
     * @throws \Bot\Http\Exception\TelegramException
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function registerWebhook(string $url): static
    {
        $this->container->get(Client::class)?->setWebhook($url);

        return $this;
    }
}
