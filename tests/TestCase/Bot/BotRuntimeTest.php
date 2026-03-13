<?php

declare(strict_types=1);

namespace BotTest\TestCase\Bot;

use Bot\Bot;
use Bot\DTO\Update\MessageUpdateDTO;
use Bot\Event\EventManagerInterface;
use Bot\Http\ClientInterface;
use Bot\Middleware\MiddlewareManagerInterface;
use Bot\Routing\RouterInterface;
use Bot\Webhook\WebhookHandlerInterface;
use BotTest\Fixture\TestActionWithAttribute;
use BotTest\Fixture\TestCommandWithAttribute;
use BotTest\Fixture\TestListener;
use BotTest\Fixture\TestMiddlewareA;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class BotRuntimeTest extends TestCase
{
    /**
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Exception
     */
    public function testRegistrationHelpers(): void
    {
        $bot = Bot::create('token');

        TestMiddlewareA::reset();
        TestListener::reset();

        $bot->withMiddleware(TestMiddlewareA::class)
            ->withCommand(TestCommandWithAttribute::class)
            ->withAction(TestActionWithAttribute::class)
            ->withListener(TestListener::class);

        $this->assertTrue(true);
    }

    /**
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Exception
     */
    public function testRunFromWebhookFlow(): void
    {
        $bot = Bot::create('token');

        $webhook = $this->createMock(WebhookHandlerInterface::class);
        $middleware = $this->createMock(MiddlewareManagerInterface::class);
        $router = $this->createMock(RouterInterface::class);
        $events = $this->createMock(EventManagerInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $payload = [
            'update_id' => 123456,
            'message' => [
                'message_id' => 1,
                'date' => 1233456789,
                'text' => '/x',
                'chat' => [
                    'id' => 123456789,
                    'type' => 'private',
                    'first_name' => 'Test User',
                ],
            ],
        ];

        $webhook->expects($this->once())
                ->method('handle')
                ->willReturn($payload);

        $middleware->expects($this->once())
                   ->method('process')
                   ->with(
                       $this->callback(fn($u) => $u instanceof MessageUpdateDTO && $u->message?->text === '/x'),
                       $this->isType('callable')
                   )
                   ->willReturnCallback(function (MessageUpdateDTO $u, callable $next): void {
                       $next($u);
                   });

        $router->expects($this->once())
               ->method('route')
               ->with($this->callback(fn($u) => $u instanceof MessageUpdateDTO && $u->message?->text === '/x'));

        $events->expects($this->atLeastOnce())->method('emit');

        $c = $bot->getContainer();
        $c->set(WebhookHandlerInterface::class, $webhook);
        $c->set(MiddlewareManagerInterface::class, $middleware);
        $c->set(RouterInterface::class, $router);
        $c->set(EventManagerInterface::class, $events);
        $c->set(LoggerInterface::class, $logger);

        $bot->runFromWebhook();
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testRunDelegatesToMiddlewareAndThenRouter(): void
    {
        $bot = Bot::create('token');

        $update = MessageUpdateDTO::fromArray([
            'message' => [
                'message_id' => 1,
                'date' => 1233456789,
                'text' => '/x',
                'chat' => [
                    'id' => 123456789,
                    'type' => 'private',
                    'first_name' => 'Test User',
                ],
            ],
        ], false);

        $middleware = $this->createMock(MiddlewareManagerInterface::class);
        $middleware->expects($this->once())
                   ->method('process')
                   ->with($update, $this->isType('callable'))
                   ->willReturnCallback(function (MessageUpdateDTO $u, callable $next): void {
                       $next($u);
                   });

        $router = $this->createMock(RouterInterface::class);
        $router->expects($this->once())->method('route')->with($update);

        $bot->getContainer()->set(MiddlewareManagerInterface::class, $middleware);
        $bot->getContainer()->set(RouterInterface::class, $router);

        $bot->run($update);
    }

    /**
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Exception
     */
    public function testRunFromWebhookLogsFailureWhenUpdateCreationFails(): void
    {
        $bot = Bot::create('token');

        $webhook = $this->createMock(WebhookHandlerInterface::class);
        $webhook->expects($this->once())->method('handle')->willReturn(null);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
               ->method('error')
               ->with(
                   'Webhook processing failed',
                   $this->callback(static fn(array $context): bool => $context['error'] === 'Unsupported update type')
               );

        $c = $bot->getContainer();
        $c->set(WebhookHandlerInterface::class, $webhook);
        $c->set(LoggerInterface::class, $logger);

        $bot->runFromWebhook();
    }

    /**
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Exception
     */
    public function testRegisterWebhookCallsClientSetWebhook(): void
    {
        $bot = Bot::create('token');

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
               ->method('setWebhook')
               ->with('https://example.com/webhook');

        $bot->getContainer()->set(ClientInterface::class, $client);

        $bot->registerWebhook('https://example.com/webhook');
        $this->assertTrue(true);
    }
}
