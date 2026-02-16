<?php

declare(strict_types=1);

namespace Bot\Update;

use Bot\DTO\Update\UpdateDTO;

interface UpdateFactoryInterface
{
    /**
     * @param array $data
     * @return \Bot\DTO\Update\UpdateDTO
     */
    public static function create(array $data): UpdateDTO;
}
