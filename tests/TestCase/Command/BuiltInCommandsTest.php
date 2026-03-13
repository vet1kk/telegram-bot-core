<?php

declare(strict_types=1);

namespace BotTest\TestCase\Command;

use Bot\Command\CommandManagerInterface;
use Bot\Command\Commands\HelpCommand;
use Bot\Command\Commands\StartCommand;
use Bot\DTO\Update\MessageUpdateDTO;
use Bot\Http\ClientInterface;
use Bot\Http\Message\MessageFactory;
use Bot\Http\Message\MessageFactoryInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

final class BuiltInCommandsTest extends TestCase
{
    /**
     * @return void
     */
    public function testStartCommandHandle(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $start = new StartCommand($logger);

        $factory = MessageFactory::create();

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
               ->method('sendMessage')
               ->willReturn([
                   'message_id' => 123,
                   'chat' => ['id' => 1],
               ]);

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->willReturnMap([
            [ClientInterface::class, $client],
            [MessageFactoryInterface::class, $factory],
        ]);

        $update = MessageUpdateDTO::fromArray(['message' => ['chat' => ['id' => 1], 'text' => '/start']], false)
                                  ->setContainer($container);

        $start->handle($update);

        $this->assertTrue(true);
    }

    /**
     * @return void
     */
    public function testStartCommandLogsErrorWhenReplyFails(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('error')->with('boom');

        $start = new StartCommand($logger);

        $update = $this->createMock(MessageUpdateDTO::class);
        $update->expects($this->once())->method('reply')->willThrowException(new \RuntimeException('boom'));

        $start->handle($update);
    }

    /**
     * @return void
     */
    public function testHelpCommandHandle(): void
    {
        $cm = $this->createMock(CommandManagerInterface::class);
        $cm->method('getCommands')->willReturn(['start' => 'Start']);

        $logger = $this->createMock(LoggerInterface::class);
        $help = new HelpCommand($logger, $cm);

        $factory = MessageFactory::create();

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
               ->method('sendMessage')
               ->willReturn([
                   'message_id' => 123,
                   'chat' => ['id' => 1],
               ]);

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->willReturnMap([
            [ClientInterface::class, $client],
            [MessageFactoryInterface::class, $factory],
        ]);

        $update = MessageUpdateDTO::fromArray([
            'message' => ['chat' => ['id' => 1], 'text' => '/help'],
        ], false)->setContainer($container);

        $help->handle($update);

        $this->assertTrue(true);
    }

    /**
     * @return void
     */
    public function testHelpCommandRepliesWhenNoCommandsAreAvailable(): void
    {
        $cm = $this->createMock(CommandManagerInterface::class);
        $cm->method('getCommands')->willReturn([]);

        $logger = $this->createMock(LoggerInterface::class);
        $help = new HelpCommand($logger, $cm);

        $update = $this->createMock(MessageUpdateDTO::class);
        $update->expects($this->once())
               ->method('reply')
               ->with('No commands are currently available.');

        $help->handle($update);
    }

    /**
     * @return void
     */
    public function testHelpCommandLogsErrorWhenReplyFails(): void
    {
        $cm = $this->createMock(CommandManagerInterface::class);
        $cm->method('getCommands')->willReturn(['start' => 'Start']);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
               ->method('error')
               ->with('Failed to send help message: boom');

        $help = new HelpCommand($logger, $cm);

        $update = $this->createMock(MessageUpdateDTO::class);
        $update->expects($this->once())
               ->method('reply')
               ->willThrowException(new \RuntimeException('boom'));

        $help->handle($update);
    }
}
