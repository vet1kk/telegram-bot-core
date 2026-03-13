<?php

declare(strict_types=1);

namespace BotTest\TestCase\Trait;

use Bot\Trait\OptionsTrait;
use PHPUnit\Framework\TestCase;

final class OptionsTraitTest extends TestCase
{
    public function testOptionsCrud(): void
    {
        $obj = new class {
            use OptionsTrait;
        };

        $obj->setOption('a', 1);
        $this->assertSame(1, $obj->getOption('a'));

        $obj->setOptions(['b' => 2, 'c' => 3]);
        $this->assertSame(2, $obj->getOption('b'));
        $this->assertSame(3, $obj->getOption('c'));

        $obj->unsetOption('b');
        $this->assertNull($obj->getOption('b'));

        $all = $obj->getOptions();
        $this->assertArrayHasKey('a', $all);
        $this->assertArrayHasKey('c', $all);
    }

    public function testSetOptionsCanOverrideExistingValues(): void
    {
        $obj = new class {
            use OptionsTrait;
        };

        $obj->setOptions(['a' => 1, 'b' => 2]);
        $obj->setOptions(['b' => 3], true);

        $this->assertSame(['b' => 3], $obj->getOptions());
    }
}
