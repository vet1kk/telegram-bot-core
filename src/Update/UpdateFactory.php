<?php

declare(strict_types=1);

namespace Bot\Update;

use Bot\DTO\Update\CallbackQueryUpdateDTO;
use Bot\DTO\Update\MessageUpdateDTO;
use Bot\DTO\Update\UpdateDTO;
use Bot\Enum\UpdateType;

class UpdateFactory implements UpdateFactoryInterface
{
    /**
     * @param array $data
     * @return \Bot\DTO\Update\UpdateDTO
     */
    public static function create(array $data): UpdateDTO
    {
        foreach (UpdateType::cases() as $case) {
            if (isset($data[$case->value])) {
                $className = match ($case) {
                    UpdateType::MESSAGE, UpdateType::EDITED_MESSAGE => MessageUpdateDTO::class,
                    UpdateType::CALLBACK_QUERY => CallbackQueryUpdateDTO::class,
                    default => UpdateDTO::class,
                };

                /** @var \Bot\DTO\Update\UpdateDTO $dto */
                $dto = $className::fromArray($data);

                return $dto;
            }
        }

        throw new \InvalidArgumentException('Unsupported update type');
    }
}
