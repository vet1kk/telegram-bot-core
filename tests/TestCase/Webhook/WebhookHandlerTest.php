<?php

declare(strict_types=1);

namespace BotTest\TestCase\Webhook;

use Bot\Webhook\WebhookHandler;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class WebhookHandlerTest extends TestCase
{
    /**
     * @return void
     */
    public function testHandleRawReturnsArrayForValidJson(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('error');

        $handler = new WebhookHandler($logger);

        $result = $handler->handleRaw('{"update_id":1,"message":{"text":"hi"}}');

        $this->assertIsArray($result);
        $this->assertSame(1, $result['update_id']);
        $this->assertSame('hi', $result['message']['text']);
    }

    /**
     * @return void
     */
    public function testHandleRawReturnsNullAndLogsForInvalidJson(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('error');

        $handler = new WebhookHandler($logger);

        $result = $handler->handleRaw('{invalid-json');

        $this->assertNull($result);
    }

    /**
     * @return void
     */
    public function testHandleParsesValidJsonFromInputReader(): void
    {
        $logger = $this->createMock(LoggerInterface::class);

        $handler = new class ($logger) extends WebhookHandler {
            protected function readInput(): string|false
            {
                return '{"update_id":123}';
            }
        };

        $data = $handler->handle();

        $this->assertIsArray($data);
        $this->assertSame(123, $data['update_id']);
    }

    /**
     * @return void
     */
    public function testHandleReturnsNullWhenInputReaderFails(): void
    {
        $logger = $this->createMock(LoggerInterface::class);

        $handler = new class ($logger) extends WebhookHandler {
            protected function readInput(): string|false
            {
                return false;
            }
        };

        $this->assertNull($handler->handle());
    }

    /**
     * @return void
     */
    public function testReadInputIsAccessibleThroughSubclass(): void
    {
        $logger = $this->createMock(LoggerInterface::class);

        $handler = new class ($logger) extends WebhookHandler {
            public function readInputPublic(): string|false
            {
                return $this->readInput();
            }
        };

        $this->assertIsString($handler->readInputPublic());
    }
}
