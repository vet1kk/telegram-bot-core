<?php

declare(strict_types=1);

namespace Bot\DTO\Message;

use Bot\DTO\DTO;

class LocationDTO extends DTO
{
    public ?float $longitude = null;
    public ?float $latitude = null;
    public ?float $horizontal_accuracy = null;
    public ?int $live_period = null;
    public ?int $heading = null;
    public ?int $proximity_alert_radius = null;

    protected array $required = [
        'longitude',
        'latitude',
    ];
}
