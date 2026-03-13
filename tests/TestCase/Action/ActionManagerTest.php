<?php

declare(strict_types=1);

namespace BotTest\TestCase\Action;

use Bot\Action\ActionManager;
use Bot\DTO\Update\CallbackQueryUpdateDTO;
use Bot\Event\EventManagerInterface;
use Bot\Event\Events\ActionHandledEvent;
use Bot\Event\Events\UnhandledEvent;
use BotTest\Fixture\TestActionWithAttribute;
use BotTest\Fixture\TestActionWithMultipleAttributes;
use BotTest\Fixture\TestActionWithoutAttribute;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

final class ActionManagerTest extends TestCase
{
    /**
     * @return void
     * @throws \ReflectionException
     */
    public function testRegisterThrowsWhenAttributeMissing(): void
    {
        $manager = $this->createManager();
        $this->expectException(\InvalidArgumentException::class);

        $manager->register(TestActionWithoutAttribute::class);
    }

    /**
     * @return void
     * @throws \ReflectionException
     */
    public function testRegisterThrowsWhenMultipleAttributesArePresent(): void
    {
        $manager = $this->createManager();
        $this->expectException(\LogicException::class);

        $manager->register(TestActionWithMultipleAttributes::class);
    }

    /**
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function testResolveReturnsNullWhenDataMissingOrNotRegistered(): void
    {
        $manager = $this->createManager();
        $u1 = CallbackQueryUpdateDTO::fromArray(['callback_query' => []], false);
        $u2 = CallbackQueryUpdateDTO::fromArray(['callback_query' => ['data' => 'x']], false);

        $this->assertNull($manager->resolve($u1));
        $this->assertNull($manager->resolve($u2));
    }

    /**
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface|\ReflectionException
     */
    public function testResolveReturnsActionWhenRegisteredAndMatched(): void
    {
        $action = new TestActionWithAttribute();

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->with(TestActionWithAttribute::class)->willReturn($action);

        $manager = $this->createManager($container);
        $manager->register(TestActionWithAttribute::class);

        $update = CallbackQueryUpdateDTO::fromArray(['callback_query' => ['data' => 'confirm']], false);

        $this->assertSame($action, $manager->resolve($update));
    }

    /**
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function testHandleEmitsUnhandledWhenNoActionMatched(): void
    {
        $eventManager = $this->createMock(EventManagerInterface::class);
        $eventManager->expects($this->once())->method('emit')->with($this->isInstanceOf(UnhandledEvent::class));

        $manager = $this->createManager(eventManager: $eventManager);
        $update = CallbackQueryUpdateDTO::fromArray(['callback_query' => ['data' => 'missing']], false);

        $manager->handle($update);
    }

    /**
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface|\ReflectionException
     */
    public function testHandleExecutesActionLogsAndEmitsHandledEvent(): void
    {
        $action = new TestActionWithAttribute();

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->with(TestActionWithAttribute::class)->willReturn($action);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info');

        $eventManager = $this->createMock(EventManagerInterface::class);
        $eventManager->expects($this->once())->method('emit')->with($this->isInstanceOf(ActionHandledEvent::class));

        $manager = $this->createManager($container, $logger, $eventManager);
        $manager->register(TestActionWithAttribute::class);

        $update = CallbackQueryUpdateDTO::fromArray(['callback_query' => ['data' => 'confirm']], false);
        $manager->handle($update);

        $this->assertTrue($action->handled);
    }

    /**
     * Helper method to create an ActionManager with optional mocked dependencies.
     *
     * @param ContainerInterface|null $container
     * @param LoggerInterface|null $logger
     * @param EventManagerInterface|null $eventManager
     * @return ActionManager
     */
    private function createManager(
        ?ContainerInterface $container = null,
        ?LoggerInterface $logger = null,
        ?EventManagerInterface $eventManager = null
    ): ActionManager {
        $container ??= $this->createMock(ContainerInterface::class);
        $logger ??= $this->createMock(LoggerInterface::class);
        $eventManager ??= $this->createMock(EventManagerInterface::class);

        return new ActionManager($container, $logger, $eventManager);
    }
}
