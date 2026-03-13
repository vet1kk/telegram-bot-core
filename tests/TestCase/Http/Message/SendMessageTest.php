<?php

declare(strict_types=1);

namespace BotTest\TestCase\Http\Message;

use Bot\Http\Message\SendMessage;
use Bot\Keyboard\KeyboardInterface;
use Bot\Keyboard\ReplyKeyboard;
use PHPUnit\Framework\TestCase;

final class SendMessageTest extends TestCase
{
    /**
     * @return void
     * @throws \JsonException
     */
    public function testCreateAndSerializeWithRequiredFields(): void
    {
        $msg = SendMessage::create()
                          ->setChatId(123)
                          ->setText('Hello');

        $data = $msg->jsonSerialize();

        $this->assertSame(123, $data['chat_id']);
        $this->assertSame('Hello', $data['text']);
    }

    /**
     * @return void
     * @throws \JsonException
     */
    public function testSerializeIncludesReplyMarkupWhenKeyboardProvided(): void
    {
        $keyboard = ReplyKeyboard::create();
        $msg = SendMessage::create()
                          ->setChatId(1)
                          ->setText('x')
                          ->setKeyboard($keyboard);

        $data = $msg->jsonSerialize(false);

        $this->assertArrayHasKey('reply_markup', $data);
        $this->assertIsString($data['reply_markup']);
    }

    /**
     * @return void
     * @throws \JsonException
     */
    public function testOptionsAreMergedIntoSerializedPayload(): void
    {
        $msg = SendMessage::create()
                          ->setChatId(1)
                          ->setText('x')
                          ->setOption('parse_mode', 'HTML')
                          ->setOption('disable_web_page_preview', true);

        $data = $msg->jsonSerialize();

        $this->assertSame('HTML', $data['parse_mode']);
        $this->assertTrue($data['disable_web_page_preview']);
    }

    /**
     * @return void
     * @throws \JsonException
     */
    public function testSetOptionsReplacesPayloadOptions(): void
    {
        $msg = SendMessage::create()
                          ->setChatId(1)
                          ->setText('x')
                          ->setOptions(['parse_mode' => 'Markdown']);

        $data = $msg->jsonSerialize();

        $this->assertSame('Markdown', $data['parse_mode']);
    }

    /**
     * @return void
     */
    public function testValidateThrowsWhenChatIdMissing(): void
    {
        $msg = SendMessage::create()->setText('x');

        $this->expectException(\InvalidArgumentException::class);
        $msg->validate();
    }

    /**
     * @return void
     */
    public function testValidateThrowsWhenTextMissing(): void
    {
        $msg = SendMessage::create()->setChatId(1);

        $this->expectException(\InvalidArgumentException::class);
        $msg->validate();
    }

    /**
     * @return void
     */
    public function testValidateThrowsWhenKeyboardIsInvalid(): void
    {
        $keyboard = $this->createMock(KeyboardInterface::class);
        $keyboard->method('isValid')->willReturn(false);

        $msg = SendMessage::create()
                          ->setChatId(1)
                          ->setText('x')
                          ->setKeyboard($keyboard);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid keyboard');
        $msg->validate();
    }
}
