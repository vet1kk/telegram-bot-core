<?php

declare(strict_types=1);

namespace BotTest\TestCase\DTO;

use Bot\DTO\DTO;
use BotTest\Fixture\TestChildDTO;
use BotTest\Fixture\TestParentDTO;
use PHPUnit\Framework\TestCase;

final class DTOTest extends TestCase
{
    /**
     * @return void
     */
    public function testFromArrayMapsScalarsAndNestedDto(): void
    {
        $dto = TestParentDTO::fromArray([
            'id' => '42',
            'name' => 123,
            'child' => [
                'value' => 'nested',
            ],
        ]);

        $this->assertInstanceOf(TestChildDTO::class, $dto->child);
        $this->assertSame(42, $dto->id);
        $this->assertSame('123', $dto->name);
        $this->assertSame('nested', $dto->child->value);
    }

    /**
     * @return void
     */
    public function testUnknownPropertiesAreStoredAsOptionsAndReturnedByGet(): void
    {
        $dto = TestParentDTO::fromArray([
            'unknown_key' => 'hello',
        ], false);

        $this->assertSame('hello', $dto->get('unknown_key'));
    }

    /**
     * @return void
     */
    public function testToArrayContainsKnownAndOptionProperties(): void
    {
        $dto = TestParentDTO::fromArray([
            'id' => 7,
            'name' => 'john',
            'unknown_key' => 'extra',
            'child' => [
                'value' => 'x',
            ],
        ]);

        $arr = $dto->toArray();

        $this->assertSame(7, $arr['id']);
        $this->assertSame('john', $arr['name']);
        $this->assertSame('extra', $arr['unknown_key']);
        $this->assertSame(['value' => 'x'], $arr['child']);
    }

    /**
     * @return void
     */
    public function testToArraySerializesArraysOfNestedDtos(): void
    {
        $dto = new class extends DTO {
            /** @var TestChildDTO[] */
            public array $children = [];
        };

        $dto->children = [
            TestChildDTO::fromArray(['value' => 'first'], false),
            TestChildDTO::fromArray(['value' => 'second'], false),
        ];

        $this->assertSame(
            ['children' => [['value' => 'first'], ['value' => 'second']]],
            $dto->toArray()
        );
        $this->assertSame(
            ['children' => [['value' => 'first'], ['value' => 'second']]],
            $dto->jsonSerialize()
        );
    }

    /**
     * @return void
     */
    public function testDefaultCreatesDtoWithoutValidation(): void
    {
        $dto = TestParentDTO::default();

        $this->assertInstanceOf(TestParentDTO::class, $dto);
    }

    /**
     * @return void
     */
    public function testGetReturnsPropertyOptionOrFallbackDefault(): void
    {
        $dto = TestParentDTO::fromArray([
            'id' => 7,
            'unknown_key' => 'extra',
        ], false);

        $this->assertSame(7, $dto->get('id'));
        $this->assertSame('extra', $dto->get('unknown_key'));
        $this->assertSame('fallback', $dto->get('missing', 'fallback'));
    }

    /**
     * @return void
     */
    public function testSetCastsBuiltinTypesAndSupportsOptions(): void
    {
        $dto = new class extends DTO {
            public int $int = 0;
            public float $float = 0.0;
            public string $string = '';
            public bool $bool = false;
            public array $array = [];
            public object $object;

            public function __construct()
            {
                $this->object = (object)[];
            }
        };

        $dto->set('int', '5')
            ->set('float', '1.5')
            ->set('string', 12)
            ->set('bool', 1)
            ->set('array', 'value')
            ->set('object', ['key' => 'value'])
            ->set('extra', 'option');

        $this->assertSame(5, $dto->int);
        $this->assertSame(1.5, $dto->float);
        $this->assertSame('12', $dto->string);
        $this->assertTrue($dto->bool);
        $this->assertSame(['value'], $dto->array);
        $this->assertSame('value', $dto->object->key);
        $this->assertSame('option', $dto->getOption('extra'));
    }

    /**
     * @return void
     */
    public function testValidateThrowsForNullAndEmptyArrayRequiredProperties(): void
    {
        $dto = new class extends DTO {
            public ?string $name = null;
            public array $items = [];

            protected array $required = ['name', 'items'];
        };

        try {
            $dto->validate();
            self::fail('Expected null required property validation to fail.');
        } catch (\InvalidArgumentException $exception) {
            self::assertSame('Property `name` is required', $exception->getMessage());
        }

        $dto->name = 'ok';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Array property `items` can not be empty');
        $dto->validate();
    }
}
