<?php

declare(strict_types=1);

namespace Bot;

use Bot\Action\ActionManagerInterface;
use Bot\Command\CommandManagerInterface;
use Bot\DTO\Update\UpdateDTO;
use Bot\Event\EventManagerInterface;
use Bot\Event\Events\ReceivedEvent;
use Bot\Http\ClientInterface;
use Bot\Middleware\MiddlewareInterface;
use Bot\Middleware\MiddlewareManagerInterface;
use Bot\Provider\CoreServiceProvider;
use Bot\Provider\ServiceProviderInterface;
use Bot\Routing\RouterInterface;
use Bot\Trait\ServiceGetterTrait;
use Bot\Update\UpdateFactoryInterface;
use Bot\Webhook\WebhookHandlerInterface;
use DI\Container;
use DI\ContainerBuilder;
use Psr\Log\LoggerInterface;

final class Bot
{
    use ServiceGetterTrait;

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
    public static function create(string $token, array $options = []): self
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
     * @return \Bot\Action\ActionManagerInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function getActionManager(): ActionManagerInterface
    {
        return $this->getService(ActionManagerInterface::class);
    }

    /**
     * @return \Bot\Command\CommandManagerInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function getCommandManager(): CommandManagerInterface
    {
        return $this->getService(CommandManagerInterface::class);
    }

    /**
     * @return \Bot\Routing\RouterInterface
     ** @throws \Psr\Container\ContainerExceptionInterface
     */
    public function getRouter(): RouterInterface
    {
        return $this->getService(RouterInterface::class);
    }

    /**
     * @return \Bot\Event\EventManagerInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function getEventManager(): EventManagerInterface
    {
        return $this->getService(EventManagerInterface::class);
    }

    /**
     * @return \Bot\Update\UpdateFactoryInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function getUpdateFactory(): UpdateFactoryInterface
    {
        return $this->getService(UpdateFactoryInterface::class);
    }

    /**
     * @return \Bot\Middleware\MiddlewareManagerInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function getMiddlewareManager(): MiddlewareManagerInterface
    {
        return $this->getService(MiddlewareManagerInterface::class);
    }

    /**
     * @param \Bot\Provider\ServiceProviderInterface $provider
     * @return $this
     */
    public function withServiceProvider(ServiceProviderInterface $provider): self
    {
        $provider->register($this->getContainer());

        return $this;
    }

    /**
     * @param class-string<MiddlewareInterface> $middleware
     * @return self
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function withMiddleware(string $middleware): self
    {
        $this->getMiddlewareManager()
             ->register($middleware);

        return $this;
    }

    /**
     * @param class-string<\Bot\Command\CommandInterface> $commandClass
     * @return self
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function withCommand(string $commandClass): self
    {
        $this->getCommandManager()
             ->register($commandClass);

        return $this;
    }

    /**
     * @param class-string<\Bot\Action\ActionInterface> $actionClass
     * @return self
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function withAction(string $actionClass): self
    {
        $this->getActionManager()
             ->register($actionClass);

        return $this;
    }

    /**
     * @param class-string<\Bot\Listener\ListenerInterface> $listenerClass
     * @return self
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function withListener(string $listenerClass): self
    {
        $this->getEventManager()
             ->registerListener($listenerClass);

        return $this;
    }

    /**
     * @param \Bot\DTO\Update\UpdateDTO $update
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function run(UpdateDTO $update): void
    {
        $destination = function (UpdateDTO $update): void {
            $this->getRouter()
                 ->route($update);
        };

        $this->getMiddlewareManager()
             ->process($update, $destination);
    }

    /**
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function runFromWebhook(): void
    {
        $rawUpdate = $this->getService(WebhookHandlerInterface::class)->handle();

        try {
            $update = $this->getUpdateFactory()
                           ->create($rawUpdate ?? [])
                           ->setContainer($this->getContainer());
            $this->getEventManager()
                 ->emit(new ReceivedEvent($update));

            $this->run($update);
        } catch (\Throwable $e) {
            $this->getService(LoggerInterface::class)->error('Webhook processing failed', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * @param string $url
     * @return static
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function registerWebhook(string $url): self
    {
        $this->getService(ClientInterface::class)->setWebhook($url);

        return $this;
    }
}
