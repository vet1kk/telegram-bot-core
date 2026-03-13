<?php

declare(strict_types=1);

namespace BotTest\TestCase\Command;

use Bot\Command\CommandManager;
use Bot\Command\CommandManagerInterface;
use Bot\DTO\Update\MessageUpdateDTO;
use Bot\Event\EventManagerInterface;
use Bot\Event\Events\CommandHandledEvent;
use Bot\Event\Events\UnhandledEvent;
use BotTest\Fixture\TestCommandWithAttribute;
use BotTest\Fixture\TestCommandWithMultipleAttributes;
use BotTest\Fixture\TestCommandWithoutAttribute;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

final class CommandManagerTest extends TestCase
{
    /**
     * @return void
     * @throws \ReflectionException
     */
    public function testRegisterThrowsWhenAttributeMissing(): void
    {
        $manager = $this->createManager();
        $this->expectException(\InvalidArgumentException::class);

        $manager->register(TestCommandWithoutAttribute::class);
    }

    /**
     * @return void
     * @throws \ReflectionException
     */
    public function testRegisterThrowsWhenMultipleAttributesArePresent(): void
    {
        $manager = $this->createManager();
        $this->expectException(\LogicException::class);

        $manager->register(TestCommandWithMultipleAttributes::class);
    }

    /**
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function testResolveReturnsNullWhenMessageTextMissingOrNotCommand(): void
    {
        $manager = $this->createManager();

        $this->assertNull($manager->resolve(MessageUpdateDTO::default()));
        $this->assertNull($manager->resolve(MessageUpdateDTO::fromArray(['message' => ['text' => 'hello']], false)));
    }

    /**
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface|\ReflectionException
     */
    public function testResolveReturnsCommandInstanceWhenRegisteredAndMatched(): void
    {
        $command = new TestCommandWithAttribute();

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->with(TestCommandWithAttribute::class)->willReturn($command);

        $manager = $this->createManager($container);
        $manager->register(TestCommandWithAttribute::class);

        $update = MessageUpdateDTO::fromArray(['message' => ['text' => '/start']], false);

        $resolved = $manager->resolve($update);

        $this->assertSame($command, $resolved);
    }

    /**
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function testHandleEmitsUnhandledWhenNoCommandMatched(): void
    {
        $eventManager = $this->createMock(EventManagerInterface::class);
        $eventManager->expects($this->once())->method('emit')->with($this->isInstanceOf(UnhandledEvent::class));

        $manager = $this->createManager(eventManager: $eventManager);
        $update = MessageUpdateDTO::fromArray(['message' => ['text' => '/unknown']], false);

        $manager->handle($update);
    }

    /**
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface|\ReflectionException
     */
    public function testHandleExecutesCommandLogsAndEmitsHandledEvent(): void
    {
        $command = new TestCommandWithAttribute();

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->with(TestCommandWithAttribute::class)->willReturn($command);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info');

        $eventManager = $this->createMock(EventManagerInterface::class);
        $eventManager->expects($this->once())->method('emit')->with($this->isInstanceOf(CommandHandledEvent::class));

        $manager = $this->createManager($container, $logger, $eventManager);
        $manager->register(TestCommandWithAttribute::class);

        $update = MessageUpdateDTO::fromArray(['message' => ['text' => '/start']], false);
        $manager->handle($update);

        $this->assertTrue($command->handled);
    }

    /**
     * @return void
     * @throws \ReflectionException
     */
    public function testGetCommandsReturnsNameDescriptionMap(): void
    {
        $manager = $this->createManager();
        $manager->register(TestCommandWithAttribute::class);

        $commands = $manager->getCommands();

        $this->assertSame(['start' => 'Start command'], $commands);
    }

    /**
     * @param \Psr\Container\ContainerInterface|null $container
     * @param \Psr\Log\LoggerInterface|null $logger
     * @param \Bot\Event\EventManagerInterface|null $eventManager
     * @return CommandManagerInterface
     */
    private function createManager(
        ?ContainerInterface $container = null,
        ?LoggerInterface $logger = null,
        ?EventManagerInterface $eventManager = null
    ): CommandManager {
        $container ??= $this->createMock(ContainerInterface::class);
        $logger ??= $this->createMock(LoggerInterface::class);
        $eventManager ??= $this->createMock(EventManagerInterface::class);

        return new CommandManager($container, $logger, $eventManager);
    }
}
