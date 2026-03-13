<?php

declare(strict_types=1);

namespace BotTest\TestCase\Trait;

use Bot\Http\ClientInterface;
use Bot\Http\Message\MessageFactory;
use Bot\Http\Message\MessageFactoryInterface;
use Bot\Trait\ReplyTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class ReplyTraitTest extends TestCase
{
    public function testReplyThrowsWhenContainerIsMissing(): void
    {
        $obj = new class {
            use ReplyTrait;
        };

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Container not set');

        $obj->reply('hello');
    }

    public function testReplyUsesContainerServices(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
               ->method('sendMessage')
               ->with($this->callback(function ($message): bool {
                   $payload = $message->jsonSerialize();

                   return $payload['chat_id'] === 777
                       && $payload['text'] === 'hello'
                       && $payload['parse_mode'] === 'HTML';
               }))
               ->willReturn(['ok' => true]);

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->willReturnMap([
            [ClientInterface::class, $client],
            [MessageFactoryInterface::class, MessageFactory::create()],
        ]);

        $obj = new class {
            use ReplyTrait;

            public function getChatId(): int|string|null
            {
                return 777;
            }
        };

        $result = $obj->setContainer($container)->reply('hello', options: ['parse_mode' => 'HTML']);

        $this->assertSame(['ok' => true], $result);
    }
}
