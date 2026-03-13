# Telegram Bot Core

[![PHP 8.3+](https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Packagist Version](https://img.shields.io/packagist/v/vet1kk/telegram-bot-core?logo=packagist&logoColor=white)](https://packagist.org/packages/vet1kk/telegram-bot-core)
[![Packagist Downloads](https://img.shields.io/packagist/dt/vet1kk/telegram-bot-core?logo=composer&logoColor=white)](https://packagist.org/packages/vet1kk/telegram-bot-core)
[![Telegram Bot API](https://img.shields.io/badge/Telegram-Bot%20API-26A5E4?logo=telegram&logoColor=white)](https://core.telegram.org/bots/api)
[![PHPUnit](https://img.shields.io/badge/tests-PHPUnit%209-0A9EDC?logo=phpunit&logoColor=white)](./phpunit.xml)
[![Psalm](https://img.shields.io/badge/static%20analysis-Psalm-4F2D7F)](./psalm.xml)
[![Type Coverage](https://img.shields.io/badge/type%20coverage-99.7%25-brightgreen)](./psalm.xml)
[![Line Coverage](https://img.shields.io/badge/line%20coverage-98.2%25-brightgreen)](./phpunit.xml)
[![License](https://img.shields.io/github/license/vet1kk/telegram-bot-core)](./LICENSE)

Lightweight Telegram bot core for PHP 8.3+ with typed updates, attribute-based handlers, middleware, events, and a small
DI-driven runtime.

It is designed for projects that want Telegram bot wiring without bringing in a full framework.

## Highlights

- Strictly typed PHP 8.3 codebase
- Attribute-based commands, actions, and listeners
- Typed DTO tree for Telegram updates
- Middleware pipeline for cross-cutting behavior
- Event manager for decoupled side effects
- Inline and reply keyboard builders
- PSR-11 container support through `php-di/php-di`
- Covered by PHPUnit tests and Psalm static analysis

## Requirements

- PHP `8.3+`
- Extensions: `curl`, `json`, `mbstring`
- A Telegram bot token from [@BotFather](https://t.me/BotFather)
- A public webhook URL for production webhook handling

## Installation

```bash
composer require vet1kk/telegram-bot-core
```

## Quick start

Create a webhook entrypoint and register your handlers. The library bootstraps its core services for you.

```php
<?php

declare(strict_types=1);

use Bot\Bot;
use App\Command\StartCommand;
use App\Action\MenuAction;
use App\Listener\FallbackListener;
use App\Middleware\TimingMiddleware;
use App\Provider\AppServiceProvider;

require __DIR__ . '/vendor/autoload.php';

Bot::create($_ENV['TELEGRAM_BOT_TOKEN'])
    ->withServiceProvider(new AppServiceProvider())
    ->withMiddleware(TimingMiddleware::class)
    ->withCommand(StartCommand::class)
    ->withAction(MenuAction::class)
    ->withListener(FallbackListener::class)
    ->runFromWebhook();
```

If you only need to register the webhook once:

```php
<?php

declare(strict_types=1);

use Bot\Bot;

require __DIR__ . '/vendor/autoload.php';

Bot::create($_ENV['TELEGRAM_BOT_TOKEN'])
    ->registerWebhook('https://example.com/telegram/webhook.php');
```

## Your first command

Commands handle Telegram messages such as `/start`.

```php
<?php

declare(strict_types=1);

namespace App\Command;

use Bot\Attribute\Command;
use Bot\Command\CommandInterface;
use Bot\DTO\Update\MessageUpdateDTO;

#[Command(name: 'start', description: 'Send a welcome message')]
final class StartCommand implements CommandInterface
{
    public function handle(MessageUpdateDTO $update): void
    {
        $update->reply('Hello! Your bot is running.');
    }
}
```

## Your first action

Actions handle `callback_query` updates from inline keyboard buttons.

```php
<?php

declare(strict_types=1);

namespace App\Action;

use Bot\Action\ActionInterface;
use Bot\Attribute\Action;
use Bot\DTO\Update\CallbackQueryUpdateDTO;
use Bot\Keyboard\Buttons\InlineButton;
use Bot\Keyboard\InlineKeyboard;

#[Action(name: 'menu', description: 'Open the main menu')]
final class MenuAction implements ActionInterface
{
    public function handle(CallbackQueryUpdateDTO $update, array $params = []): void
    {
        $keyboard = InlineKeyboard::create()
            ->addButton(
                InlineButton::create()
                    ->setText('Help')
                    ->setCallbackData('help'),
                1
            )
            ->addButton(
                InlineButton::create()
                    ->setText('Settings')
                    ->setCallbackData('settings'),
                1
            );

        $update->reply('Choose an option:', $keyboard);
    }
}
```

## Middleware

Middleware runs before the router dispatches an update and is useful for timing, auth, maintenance mode, or logging.

```php
<?php

declare(strict_types=1);

namespace App\Middleware;

use Bot\DTO\Update\UpdateDTO;
use Bot\Middleware\MiddlewareInterface;
use Psr\Log\LoggerInterface;

final class TimingMiddleware implements MiddlewareInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function process(UpdateDTO $update, callable $next): void
    {
        $startedAt = microtime(true);

        $next($update);

        $this->logger->info('Update handled', [
            'duration_ms' => (int) ((microtime(true) - $startedAt) * 1000),
        ]);
    }
}
```

## Event listeners

Listeners let you react to lifecycle events without coupling side effects to your handlers.

Built-in events include:

- `Bot\Event\Events\ReceivedEvent`
- `Bot\Event\Events\CommandHandledEvent`
- `Bot\Event\Events\ActionHandledEvent`
- `Bot\Event\Events\UnhandledEvent`

```php
<?php

declare(strict_types=1);

namespace App\Listener;

use Bot\Attribute\Listener;
use Bot\Event\Events\UnhandledEvent;
use Bot\Listener\ListenerInterface;
use Psr\Log\LoggerInterface;

final class FallbackListener implements ListenerInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    #[Listener(eventClass: UnhandledEvent::class)]
    public function onUnhandled(UnhandledEvent $event): void
    {
        $this->logger->info('Unhandled update received');

        $update = $event->getUpdate();
        if ($update->getChatId() !== null) {
            $update->reply("Sorry, I didn't understand that message.");
        }
    }
}
```

## Service providers and customization

The core runtime registers sensible defaults, including a `NullLogger`, webhook handler, client, router, managers, and
update factory. Override or extend them with your own service provider.

```php
<?php

declare(strict_types=1);

namespace App\Provider;

use Bot\Provider\ServiceProviderInterface;
use Bot\Webhook\WebhookHandlerInterface;
use DI\Container;
use App\Webhook\FrameworkWebhookHandler;

final class AppServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->set(
            WebhookHandlerInterface::class,
            \DI\autowire(FrameworkWebhookHandler::class)
        );
    }
}
```

If you are integrating this library into Laravel, Symfony, Slim, or another HTTP stack, swapping
`Bot\Webhook\WebhookHandlerInterface` is usually the first extension point.

## Running updates manually

For tests or custom transports, you can create an `UpdateDTO` and execute the pipeline directly with
`Bot::run($update)`.

`Bot::runFromWebhook()` is just a convenience around reading the webhook payload, building the update DTO, emitting
`ReceivedEvent`, and routing the update through middleware.

## Development commands

```bash
composer test
composer test:coverage
composer psalm
composer psalm:stats
composer phpcs
composer phpcbf
```

## Quality snapshot

Current local quality snapshot for this repository:

- Psalm inferred types for `99.7207%` of the codebase
- PHPUnit suite: `106` tests, `240` assertions
- PHPUnit coverage: `98.17%` lines, `94.93%` methods, `85.37%` classes

You can refresh these numbers locally with:

```bash
composer psalm
composer test:coverage
```

## Project notes

- Namespace: `Bot\\`
- License: `MIT`
- Homepage: `https://github.com/vet1kk/telegram-bot-core`
- Issue tracker: `https://github.com/vet1kk/telegram-bot-core/issues`

## Contributing

Issues and pull requests are welcome. If you add new features or integrations, please include tests where it makes sense
so the public API stays reliable.
