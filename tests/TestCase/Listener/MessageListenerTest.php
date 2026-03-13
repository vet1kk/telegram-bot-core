<?php

declare(strict_types=1);

namespace BotTest\TestCase\Listener;

use Bot\DTO\Update\UpdateDTO;
use Bot\Event\Events\UnhandledEvent;
use Bot\Listener\MessageListener;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class MessageListenerTest extends TestCase
{
    /**
     * @return void
     */
    public function testOnUnhandledEventLogsInfo(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info');

        $listener = new MessageListener($logger);
        $update = UpdateDTO::fromArray([], false);
        $event = new UnhandledEvent($update);

        $listener->onUnhandledEvent($event);
    }

    /**
     * @return void
     */
    public function testOnUnhandledEventRepliesWhenChatIdExists(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info');
        $logger->expects($this->never())->method('error');

        $listener = new MessageListener($logger);

        $update = $this->createMock(UpdateDTO::class);
        $update->method('getChatId')->willReturn(123);
        $update->expects($this->once())
               ->method('reply')
               ->with("Sorry, I didn't understand that message.");

        $listener->onUnhandledEvent(new UnhandledEvent($update));
    }

    /**
     * @return void
     */
    public function testOnUnhandledEventLogsErrorWhenReplyFails(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info');
        $logger->expects($this->once())->method('error')->with('boom');

        $listener = new MessageListener($logger);

        $update = $this->createMock(UpdateDTO::class);
        $update->method('getChatId')->willReturn(123);
        $update->expects($this->once())
               ->method('reply')
               ->willThrowException(new \RuntimeException('boom'));

        $listener->onUnhandledEvent(new UnhandledEvent($update));
    }
}
