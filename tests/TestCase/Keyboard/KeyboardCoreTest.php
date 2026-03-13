<?php

declare(strict_types=1);

namespace BotTest\TestCase\Keyboard;

use Bot\Keyboard\Buttons\InlineButton;
use Bot\Keyboard\Buttons\ReplyButton;
use Bot\Keyboard\InlineKeyboard;
use Bot\Keyboard\KeyboardRemove;
use PHPUnit\Framework\TestCase;

final class KeyboardCoreTest extends TestCase
{
    /**
     * @return void
     */
    public function testKeyboardAddButtonsBatch(): void
    {
        $kbd = InlineKeyboard::create()
                             ->addButtons(
                                 [
                                     ReplyButton::create()->setText('A'),
                                     InlineButton::create()->setText('B')->setCallbackData('cb'),
                                 ]
                             );
        $data = $kbd->jsonSerialize();

        $this->assertCount(2, $data['inline_keyboard'][0]);
    }

    /**
     * @return void
     */
    public function testInlineKeyboardSerializeShape(): void
    {
        $kbd = InlineKeyboard::create()->addButton(
            InlineButton::create()->setText('Click')->setCallbackData('cb')
        );
        $data = $kbd->jsonSerialize();

        $this->assertArrayHasKey('inline_keyboard', $data);
        $this->assertIsArray($data['inline_keyboard']);
        $this->assertCount(1, $data['inline_keyboard'][0]);
    }

    /**
     * @return void
     */
    public function testInlineKeyboardMultipleRows(): void
    {
        $kbd = InlineKeyboard::create()
                             ->addButton(InlineButton::create()->setText('A')->setCallbackData('cb1'))
                             ->addButton(InlineButton::create()->setText('B')->setCallbackData('cb2'), 2);
        $data = $kbd->jsonSerialize();

        $this->assertCount(1, $data['inline_keyboard'][0]);
        $this->assertCount(1, $data['inline_keyboard'][1]);
    }

    /**
     * @return void
     */
    public function testAddButtonThrowsWhenProvidedButtonIsInvalid(): void
    {
        $button = $this->createMock(\Bot\Keyboard\Buttons\ButtonInterface::class);
        $button->method('isValid')->willReturn(false);

        $keyboard = InlineKeyboard::create();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The provided button structure is invalid');
        $keyboard->addButton($button);
    }

    /**
     * @return void
     */
    public function testAddButtonsThrowsWhenArrayContainsNonButtons(): void
    {
        $keyboard = InlineKeyboard::create();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('All elements in the buttons array must implement ButtonInterface');
        /** @noinspection PhpParamsInspection */
        $keyboard->addButtons(['invalid']);
    }

    /**
     * @return void
     */
    public function testKeyboardRemoveSerializeAndValidity(): void
    {
        $k = KeyboardRemove::create()->setRemove(true)->setSelective(true);
        $data = $k->jsonSerialize();

        $this->assertTrue($k->isValid());
        $this->assertTrue($data['remove_keyboard']);
        $this->assertTrue($data['selective']);
    }

    /**
     * @return void
     */
    public function testIsValidReturnsFalseWhenKeyboardEmpty(): void
    {
        $k = InlineKeyboard::create();
        $this->assertFalse($k->isValid());
    }

    /**
     * @return void
     */
    public function testIsValidReturnsTrueWhenKeyboardHasButtons(): void
    {
        $k = InlineKeyboard::create();
        $k->addButton(InlineButton::create()->setText('Test')->setCallbackData('cb'));
        $this->assertTrue($k->isValid());
    }
}
