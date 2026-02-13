<?php

declare(strict_types=1);

namespace Bot\Provider;

use Bot\Command\CommandManager;
use Bot\Event\EventManager;
use Bot\Http\Client;
use Bot\Middleware\MiddlewareManager;
use Bot\Routing\Router;
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
        $logger = $this->options['logger'] ?? new NullLogger();
        $container->set(LoggerInterface::class, $logger);

        $container->set(Client::class, new Client($this->token, $this->options));

        $container->set(CommandManager::class, \DI\autowire(CommandManager::class));
        $container->set(MiddlewareManager::class, \DI\autowire(MiddlewareManager::class));
        $container->set(EventManager::class, \DI\autowire(EventManager::class));
        $container->set(Router::class, \DI\autowire(Router::class));
    }
}
