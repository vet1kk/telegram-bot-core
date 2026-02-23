<?php

declare(strict_types=1);

namespace Bot\Trait;

use Bot\Http\ClientInterface;
use Bot\Http\Message\MessageFactoryInterface;
use Bot\Keyboard\KeyboardInterface;
use Psr\Container\ContainerInterface;

trait ReplyTrait
{
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
     * @param array $options
     * @return array
     * @throws \LogicException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function reply(string $text, ?KeyboardInterface $keyboard = null, array $options = []): array
    {
        if (!$this->container) {
            throw new \LogicException('Container not set');
        }

        /**
         * @var \Bot\Http\ClientInterface $client
         * @var \Bot\Http\Message\MessageFactoryInterface $factory
         */
        $client = $this->container->get(ClientInterface::class);
        $factory = $this->container->get(MessageFactoryInterface::class);

        return $client->sendMessage(
            $factory->create()
                    ->setChatId($this->getChatId())
                    ->setText($text)
                    ->setKeyboard($keyboard)
                    ->setOptions($options)
        );
    }
}
