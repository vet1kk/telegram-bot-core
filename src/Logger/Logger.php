<?php

declare(strict_types=1);

namespace Bot\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Logger
{
    protected static ?LoggerInterface $logger = null;

    /**
     * @param \Psr\Log\LoggerInterface|null $logger
     * @return void
     */
    public static function setLogger(?LoggerInterface $logger): void
    {
        self::$logger = $logger;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public static function getLogger(): LoggerInterface
    {
        if (self::$logger) {
            return self::$logger;
        }

        self::$logger = new NullLogger();

        return self::$logger;
    }

    /**
     * @param string $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function log(string $level, string $message, array $context = []): void
    {
        static::getLogger()->log($level, $message, $context);
    }
}
