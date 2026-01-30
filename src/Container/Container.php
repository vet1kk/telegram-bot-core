<?php

namespace Bot\Container;

use Psr\Container\ContainerInterface;
use ReflectionClass;

class Container implements ContainerInterface
{
    protected array $instances = [];

    /**
     * @param string $id
     * @param object $instance
     * @return void
     */
    public function set(string $id, object $instance): void
    {
        $this->instances[$id] = $instance;
    }

    /**
     * @param string $id
     * @return object
     * @throws \Exception
     */
    public function get(string $id): object
    {
        if ($this->has($id)) {
            return $this->instances[$id];
        }

        return $this->build($id);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return !empty($this->instances[$id]);
    }

    /**
     * @param string $id
     * @return object
     * @throws \Exception
     */
    protected function build(string $id): object
    {
        $reflection = new ReflectionClass($id);

        if (!$reflection->isInstantiable()) {
            throw new \RuntimeException("Class $id is not instantiable.");
        }

        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            return new $id();
        }

        $parameters = $constructor->getParameters();
        $dependencies = $this->resolveDependencies($parameters);

        $instance = $reflection->newInstanceArgs($dependencies);
        $this->instances[$id] = $instance;

        return $instance;
    }

    /**
     * @param array<\ReflectionParameter> $parameters
     * @return array
     * @throws \Exception
     */
    protected function resolveDependencies(array $parameters): array
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if (!$type || $type->isBuiltin()) {
                throw new \RuntimeException("Cannot resolve non-class dependency: {$parameter->getName()}");
            }

            $dependencies[] = $this->get($type->getName());
        }

        return $dependencies;
    }
}
