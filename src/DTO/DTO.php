<?php

declare(strict_types=1);

namespace Bot\DTO;

use Bot\Trait\OptionsTrait;

/**
 * @template T of \Bot\DTO\DTO
 */
abstract class DTO implements \JsonSerializable
{
    use OptionsTrait;

    protected array $required = [];

    /**
     * @return self
     * @phpstan-return T
     */
    public static function default(): self
    {
        return self::fromArray(validate: false);
    }

    /**
     * @param array $data
     * @param bool $validate
     * @return self
     * @phpstan-return T
     */
    public static function fromArray(array $data = [], bool $validate = true): self
    {
        $self = new static();
        foreach ($data as $key => $value) {
            $self->set($key, $value);
        }
        if ($validate) {
            $self->validate();
        }

        return $self;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $result = [];
        $properties = get_object_vars($this);

        unset($properties['required'], $properties['options']);

        foreach ($properties as $key => $value) {
            if ($value instanceof self) {
                $result[$key] = $value->toArray();
            } elseif (is_array($value)) {
                $result[$key] = array_map(
                    static fn(mixed $item) => $item instanceof self ? $item->toArray() : $item,
                    $value
                );
            } else {
                $result[$key] = $value;
            }
        }

        return array_merge($result, $this->getOptions());
    }

    /**
     * @return array
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
     * @return self
     */
    public function set(string $property, mixed $value = null): self
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
                $value = $className::fromArray($value);
            }
        }

        if ($type instanceof \ReflectionNamedType && $type->isBuiltin()) {
            $value = match ($type->getName()) {
                'int' => (int)$value,
                'string' => (string)$value,
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
}
