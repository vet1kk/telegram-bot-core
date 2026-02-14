<?php

declare(strict_types=1);

namespace Bot\Event\Events;

use Bot\Action\ActionInterface;
use Bot\DTO\Update\CallbackQueryUpdateDTO;
use Bot\Event\EventInterface;

class ActionHandledEvent implements EventInterface
{
    /**
     * @param \Bot\Action\ActionInterface $action
     * @param \Bot\DTO\Update\CallbackQueryUpdateDTO $update
     */
    public function __construct(protected ActionInterface $action, protected CallbackQueryUpdateDTO $update)
    {
    }

    /**
     * @return \Bot\Action\ActionInterface
     */
    public function getAction(): ActionInterface
    {
        return $this->action;
    }

    /**
     * @return \Bot\DTO\Update\CallbackQueryUpdateDTO
     */
    public function getUpdate(): CallbackQueryUpdateDTO
    {
        return $this->update;
    }
}
