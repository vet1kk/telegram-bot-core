<?php

declare(strict_types=1);

namespace Bot\Trait;

use Bot\Http\ClientInterface;
use Bot\Http\Message\MessageFactoryInterface;
use Bot\Keyboard\KeyboardInterface;
use Psr\Container\ContainerInterface;

trait ReplyTrait
{
    use ServiceGetterTrait;

    protected ?ContainerInterface $container = null;

    /**
     * @param \Psr\Container\ContainerInterface $container
     * @return static
     */
    public function setContainer(ContainerInterface $container): static
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @return int|string|null
     */
    public function getChatId(): int|string|null
    {
        return null;
    }

    /**
     * @param string $text
     * @param \Bot\Keyboard\KeyboardInterface|null $keyboard
     * @param array<array-key, mixed> $options
     * @return array<array-key, mixed>
     * @throws \LogicException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function reply(string $text, ?KeyboardInterface $keyboard = null, array $options = []): array
    {
        $client = $this->getService(ClientInterface::class);
        $factory = $this->getService(MessageFactoryInterface::class);

        return $client->sendMessage(
            $factory::create()
                    ->setChatId($this->getChatId())
                    ->setText($text)
                    ->setKeyboard($keyboard)
                    ->setOptions($options)
        );
    }

    /**
     * @return \Psr\Container\ContainerInterface
     * @throws \LogicException
     */
    protected function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            throw new \LogicException('Container not set');
        }

        return $this->container;
    }
}
