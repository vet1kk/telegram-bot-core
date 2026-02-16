<?php

declare(strict_types=1);

namespace Bot\Provider;

use Bot\Action\ActionManager;
use Bot\Action\ActionManagerInterface;
use Bot\Command\CommandManager;
use Bot\Command\CommandManagerInterface;
use Bot\Config\ConfigService;
use Bot\Config\ConfigServiceInterface;
use Bot\Event\EventManager;
use Bot\Event\EventManagerInterface;
use Bot\Http\Client;
use Bot\Http\ClientInterface;
use Bot\Middleware\MiddlewareManager;
use Bot\Middleware\MiddlewareManagerInterface;
use Bot\Routing\Router;
use Bot\Routing\RouterInterface;
use Bot\Update\UpdateFactory;
use Bot\Update\UpdateFactoryInterface;
use Bot\Webhook\WebhookHandler;
use Bot\Webhook\WebhookHandlerInterface;
use DI\Container;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class CoreServiceProvider implements ServiceProviderInterface
{
    /**
     * @param string $token
     * @param array $options
     */
    public function __construct(protected string $token, protected array $options)
    {
    }

    /**
     * @param \DI\Container $container
     * @return void
     */
    public function register(Container $container): void
    {
        $container->set(LoggerInterface::class, fn() => new NullLogger());
        $container->set(WebhookHandlerInterface::class, \DI\autowire(WebhookHandler::class));
        $container->set(ConfigServiceInterface::class, fn() => new ConfigService($this->options));
        $container->set(ClientInterface::class, fn() => new Client($this->token, $this->options));
        $container->set(UpdateFactoryInterface::class, \DI\autowire(UpdateFactory::class));
        $container->set(CommandManagerInterface::class, \DI\autowire(CommandManager::class));
        $container->set(ActionManagerInterface::class, \DI\autowire(ActionManager::class));
        $container->set(MiddlewareManagerInterface::class, \DI\autowire(MiddlewareManager::class));
        $container->set(EventManagerInterface::class, \DI\autowire(EventManager::class));
        $container->set(RouterInterface::class, \DI\autowire(Router::class));
        //aliasing
        $container->set(WebhookHandler::class, \DI\get(WebhookHandlerInterface::class));
        $container->set(UpdateFactory::class, \DI\get(UpdateFactoryInterface::class));
        $container->set(CommandManager::class, \DI\get(CommandManagerInterface::class));
        $container->set(ActionManager::class, \DI\get(ActionManagerInterface::class));
        $container->set(MiddlewareManager::class, \DI\get(MiddlewareManagerInterface::class));
        $container->set(EventManager::class, \DI\get(EventManagerInterface::class));
        $container->set(Router::class, \DI\get(RouterInterface::class));
    }
}
