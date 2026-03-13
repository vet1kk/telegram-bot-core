<?php

declare(strict_types=1);

namespace Bot\DTO;

use Bot\Trait\OptionsTrait;

/**
 * @psalm-consistent-constructor
 */
abstract class DTO implements \JsonSerializable
{
    use OptionsTrait;

    /** @var list<string> */
    protected array $required = [];

    /**
     * @return static
     */
    public static function default(): static
    {
        return self::fromArray(validate: false);
    }

    /**
     * @param array<array-key, mixed> $data
     * @param bool $validate
     * @return static
     */
    public static function fromArray(array $data = [], bool $validate = true): static
    {
        $self = new static();
        foreach (array_keys($data) as $key) {
            $self->set((string)$key, $data[$key], $validate);
        }
        if ($validate) {
            $self->validate();
        }

        return $self;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function toArray(): array
    {
        $properties = get_object_vars($this);

        unset($properties['required'], $properties['options']);

        /** @var array<array-key, mixed> $result */
        $result = array_map(fn(mixed $value): mixed => $this->normalizeValue($value), $properties);

        return array_merge($result, $this->getOptions());
    }

    /**
     * @return array<array-key, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @param string $property
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $property, mixed $default = null): mixed
    {
        return $this->{$property} ?? $this->getOption($property) ?? $default;
    }

    /**
     * @param string $property
     * @param mixed $value
     * @param bool $validate
     * @return self
     */
    public function set(string $property, mixed $value = null, bool $validate = true): self
    {
        if (!property_exists($this, $property)) {
            $this->setOption($property, $value);

            return $this;
        }

        $reflection = new \ReflectionProperty($this, $property);
        $type = $reflection->getType();

        if (is_array($value) && $type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
            $className = $type->getName();

            if (is_subclass_of($className, self::class)) {
                /** @var class-string<self> $className */
                $value = $className::fromArray($value, $validate);
            }
        }

        if ($type instanceof \ReflectionNamedType && $type->isBuiltin()) {
            /** @var mixed $value */
            $value = match ($type->getName()) {
                'integer', 'int' => match (true) {
                    is_int($value) => $value,
                    is_float($value), is_bool($value), is_string($value), $value === null => (int)$value,
                    is_array($value) => empty($value) ? 0 : 1,
                    default => throw new \InvalidArgumentException(
                        sprintf('Property `%s` expects an int-compatible value', $property)
                    ),
                },
                'double', 'float' => match (true) {
                    is_float($value) => $value,
                    is_int($value), is_bool($value), is_string($value), $value === null => (float)$value,
                    is_array($value) => empty($value) ? 0.0 : 1.0,
                    default => throw new \InvalidArgumentException(
                        sprintf('Property `%s` expects a float-compatible value', $property)
                    ),
                },
                'string' => is_scalar($value) || $value === null ? (string)$value : throw new \InvalidArgumentException(
                    sprintf('Property `%s` expects a string-compatible value', $property)
                ),
                'bool' => (bool)$value,
                'array' => (array)$value,
                'object' => (object)$value,
                default => $value
            };
        }

        $this->{$property} = $value;

        return $this;
    }

    /**
     * @return void
     */
    public function validate(): void
    {
        foreach ($this->required as $property) {
            if (is_array($this->{$property}) && empty($this->{$property})) {
                throw new \InvalidArgumentException(sprintf('Array property `%s` can not be empty', $property));
            }
            if (is_null($this->{$property})) {
                throw new \InvalidArgumentException(sprintf('Property `%s` is required', $property));
            }
        }
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function normalizeValue(mixed $value): mixed
    {
        if ($value instanceof self) {
            return $value->toArray();
        }

        if (!is_array($value)) {
            return $value;
        }

        /** @psalm-suppress MixedAssignment */
        return array_map(
            static fn(mixed $item): mixed => $item instanceof self ? $item->toArray() : $item,
            $value
        );
    }
}
