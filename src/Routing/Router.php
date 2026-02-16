<?php

declare(strict_types=1);

namespace Bot\Routing;

use Bot\Action\ActionManagerInterface;
use Bot\Command\CommandManagerInterface;
use Bot\DTO\Update\CallbackQueryUpdateDTO;
use Bot\DTO\Update\MessageUpdateDTO;
use Bot\DTO\Update\UpdateDTO;
use Bot\Event\EventManagerInterface;
use Bot\Event\Events\UnhandledEvent;
use Psr\Log\LoggerInterface;

class Router implements RouterInterface
{
    /**
     * @param \Bot\Command\CommandManagerInterface $commandManager
     * @param \Bot\Action\ActionManagerInterface $actionManager
     * @param \Bot\Event\EventManagerInterface $eventManager
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        protected CommandManagerInterface $commandManager,
        protected ActionManagerInterface $actionManager,
        protected EventManagerInterface $eventManager,
        protected LoggerInterface $logger
    ) {
    }

    /**
     * @param \Bot\DTO\Update\UpdateDTO $update
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function route(UpdateDTO $update): void
    {
        match (true) {
            $update instanceof MessageUpdateDTO => $this->commandManager->handle($update),
            $update instanceof CallbackQueryUpdateDTO => $this->actionManager->handle($update),
            default => $this->eventManager->emit(new UnhandledEvent($update)),
        };
    }
}
