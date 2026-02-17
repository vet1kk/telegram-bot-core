# 🌟 Telegram Bot Core (v1.0.0)

A professional, type-safe, and highly modular Telegram Bot framework for PHP 8.3+.
This framework leverages **PSR-11 Dependency Injection**, **Middleware Pipelines**, and **Attribute-based Routing** to
provide a
decoupled and testable development experience.
---

## Key Features

* **Interface-Driven Architecture**: Every core component (Client, Router, Factory) is bound to an interface for 100%
  swappability.
* **Recursive DTO Graph**: Updates are transformed into structured object trees (e.g., `Update` -> `Message` ->
  `Chat`) via Reflection.
* **Attribute Routing**: Register Commands and Actions directly on classes using native PHP attributes.
* **Middleware Stack**: Intercept and process updates globally for maintenance mode, logging, or authentication.
* **Event System**: Emit and listen to custom events for decoupled side effects like logging or analytics.
* **2D Keyboard Panel**: Build complex button grids with automatic validation and recursive JSON serialization.
* **95%+ Type Coverage**: Verified with Psalm to ensure a rock-solid, predictable codebase
* **Multiple Bot Support**: Easily manage multiple bots with separate configurations and service providers.

---

## Getting Started

## Installation

```bash
composer require vet1kk/telegram-bot-core
```

### 1. Register and Initialize the Bot

The `Bot` class is a **final orchestrator**. It initializes the `DI\Container` and registers core services via the
`CoreServiceProvider`.

```php
use Bot\Bot;

$bot = Bot::create('YOUR_TELEGRAM_TOKEN', [
    // Any optional configuration settings, can be accessed via the ConfigServiceInterface later in your commands or actions etc.
    'maintenance.enabled' => true,
]);
```

### 2. Configure the Execution Pipeline

```php
// In your webhook.php
$bot->withMiddleware(YourMiddleware::class)
    ->withCommand(YourCommand::class)
    ->withAction(YourAction::class)
    ->withListener(YourListener::class)
    ->withServiceProvider(YourServiceProvider::class);
          
// Captured by the WebhookHandlerInterface, transformed, and routed.
$bot->runFromWebhook();
```

---

## Webhook Management

### 1. Webhook Registration

Link your server's URL with the Telegram API **once** during setup.

```php
$bot->registerWebhook('https://your-domain.com/webhook.php');
```

### 2. Execution Lifecycle

#### The `runFromWebhook` method manages the complete lifecycle through decoupled services:

* **Ingestion**: `WebhookHandlerInterface` captures raw input (defaults to `php://input`).
* **Transformation**: `UpdateFactoryInterface` builds the DTO graph via Reflection.
* **Events**: Emits `ReceivedEvent` for global logging/analytics or any other job.
* **Middleware Processing**: Passes the DTO through your registered middleware stack.
* **Routing**: Delivers the update to the appropriate Command or Action handler.

--- 

## Creating Commands

Commands handle text messages starting with `/`. Define them by implementing `CommandInterface` and adding the
`#[Command]` attribute.
Use **Interfaces** for type-hinting dependencies.

```php
namespace App\Commands;

use Bot\Attribute\Command;
use Bot\Http\ClientInterface;
use Bot\Command\CommandInterface;
use Bot\DTO\Update\MessageUpdateDTO;use Bot\Http\Message\SendMessage;

#[Command(name: 'finish', description: 'Close the current session')]
class FinishCommand implements CommandInterface 
{
    public function __construct(
        protected ClientInterface $client
    ) {}

    public function handle(MessageUpdateDTO $update): void 
    {
        $message = SendMessage::create()
                              ->setChatId($update->getChatId())
                              ->setText('See you later!');
        $this->client->sendMessage($message);
    }
}

// Register in your bootstrap
$bot->withCommand(FinishCommand::class);
```

---

## Event Management

The `EventManager` decouples side effects from core logic. Listeners are scanned for the `#[Listener]` attribute.

### 1. Registering a Listener

Listeners are registered using the `withListener` method. The framework uses Reflection to scan for the `#[Listener]`
attribute on class methods to map them to specific event classes.

#### Build-in Events

* `ReceivedEvent`: Immediately after transformation.
* `CommandHandledEvent` / `ActionHandledEvent`: After successful execution.
* `UnhandledEvent`: Emitted when no command or action matches the update.

### 2. Creating Listeners

```php
use Bot\Attribute\Listener;
use Bot\Event\Events\CommandHandledEvent;
use Bot\Listener\ListenerInterface;
use App\Services\AnalyticsService;

class AnalyticsListener implements ListenerInterface
{
    public function __construct(
        // Inject any dependencies here, e.g. a service or repository that you want to use in the action.
        // The container will automatically resolve it when the action is executed.
        // (Don't forget to register the service in your service provider)
        protected AnalyticsService $analyticsService
    ) {}

    #[Listener(CommandHandledEvent::class)] // Specify the event class to listen for
    public function logCommandUsage(CommandHandledEvent $event): void
    {
        // Access the command and the update that triggered it
        $command = $event->getCommand();
        $update = $event->getUpdate();
        
        // Log or store analytics data as needed
        // For example, you could log the command name and the user who triggered it
        $this->analyticsService->trackCommandUsage($command::class, $update->getUserId());
    }
}

// Register in your bootstrap
$bot->withListener(AnalyticsListener::class);
```

### 3. Emitting Custom Events

You can also emit your own events from anywhere within your application by retrieving the `EventManager` from the
`container`.

```php
$eventManager = $container->get(EventManager::class);
$eventManager->emit(new MyCustomEvent($data));
```

---

## Creating Actions

Actions handle `callback_query` data from inline buttons. Use the `#[Action]` attribute to map the data string to a
specific class.

```php
namespace App\Actions;

use Bot\Action\ActionInterface;
use Bot\Attribute\Action;
use Bot\DTO\Update\CallbackQueryUpdateDTO;
use Bot\Http\Client;
use Bot\Keyboard\Buttons\InlineButton;
use Bot\Keyboard\InlineKeyboard;
use Bot\Http\Message\SendMessage;

#[Action(name: 'menu')]
class MenuAction implements ActionInterface 
{
     public function __construct(
        // Inject any dependencies here, e.g. a service or repository that you want to use in the action.
        // The container will automatically resolve it when the action is executed.
        // (Don't forget to register the service in your service provider)
        protected Client $client
    ) {}
    
    public function handle(CallbackQueryUpdateDTO $update): void 
    {
        // 1. Build Inline Buttons using the fluent API
        $profileBtn = InlineButton::create()
                                  ->setText('👤 Profile')
                                  ->setCallbackData('user_profile');
    
        $settingsBtn = InlineButton::create()
                                   ->setText('⚙️ Settings')
                                   ->setCallbackData('settings_menu');
    
        $helpBtn = InlineButton::create()
                               ->setText('❓ Help')
                               ->setCallbackData('help_info');
    
        // 2. Build the Keyboard grid
        $keyboard = InlineKeyboard::create()
                                  ->addButton($profileBtn, line: 1)
                                  ->addButton($settingsBtn, line: 1)
                                  ->addButton($helpBtn, line: 2);
    
        // 3. Create the specialized SendMessage obj
        $message = SendMessage::create()
                              ->setChatId($update->getChatId())
                              ->setText("Please choose an option:")
                              ->setKeyboard($keyboard);
    
        // 4. Send through the Client
        $this->client->sendMessage($message);
    }
}

// Register in your bootstrap
$bot->withAction(MenuAction::class);
```

---

## Creating Middleware

Middlewares allow you to intercept updates before they reach the router. They are processed as a pipeline, where each
layer can stop execution or pass it to the next.

```php
namespace App\Middleware;

use Bot\DTO\Update\UpdateDTO;
use Bot\Middleware\MiddlewareInterface;
use Psr\Log\LoggerInterface;

class TimerMiddleware implements MiddlewareInterface
{
    public function __construct(
        // Inject any dependencies here, e.g. a service or repository that you want to use in the action.
        // The container will automatically resolve it when the action is executed.
        // (Don't forget to register the service in your service provider)
        protected LoggerInterface $logger
    ) {}

    public function process(UpdateDTO $update, callable $next): void
    {
        $start = microtime(true);

        // 1. Pass the update down the chain
        $next($update);

        // 2. Logic after the bot has finished processing
        $duration = microtime(true) - $start;

        $this->logger->info("Update handled in " . round($duration, 4) . "s");
    }
}

// Register it in your bootstrap
$bot->withMiddleware(TimerMiddleware::class);
```

---

## Dependency Injection & Service Providers

Implement `ServiceProviderInterface` to bind your implementations to the container or override core services.

### 1. Create a Service Provider

Implement the `ServiceProviderInterface` to define how your custom classes should be instantiated.

```php
namespace App\Providers;

use Bot\Provider\ServiceProviderInterface;
use App\Services\AnalyticsService;
use DI\Container;

class UserServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        // Swap the default ingestion for a framework-specific one
        $container->set(WebhookHandlerInterface::class, \DI\autowire(LaravelWebhookHandler::class));
        
        // Register custom services
        $container->set(AnalyticsService::class, \DI\autowire(AnalyticsService::class));
    }
}
```

### Tip: The "Master Provider" Pattern

For larger applications, you can encapsulate your entire bot's logic (commands, actions, listeners, and custom services)
into a single **Master Service Provider**.
This keeps your entry-point (webhook.php) clean and your configuration centralized.

#### 1. Create a Master Provider

```php
namespace App\Providers;

use Bot\Provider\ServiceProviderInterface;
use Bot\Command\CommandManagerInterface;
use Bot\Action\ActionManagerInterface;
use Bot\Event\EventManagerInterface;
use App\Commands\{StartCommand, HelpCommand};
use App\Actions\MenuAction;
use DI\Container;

class BotServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        // Register Commands
        $container->get(CommandManagerInterface::class)
                  ->register(StartCommand::class)
                  ->register(HelpCommand::class);

        // Register Actions
        $container->get(ActionManagerInterface::class)
                  ->register(MenuAction::class);

        // Bind custom services
        $container->set(UserRepository::class, \DI\autowire(UserRepository::class));
    }
}
```

#### 2. The Result: A 2-Line Webhook

Your production entry point remains untouched regardless of how many commands you add.

```php
// webhook.php
$bot = Bot::create('TOKEN', $options)
    ->withServiceProvider(BotServiceProvider::class);

$bot->runFromWebhook();
```

---

## Multi-Bot Support

Because the framework is built on an isolated **Dependency Injection Container**, you can manage multiple bot instances
simultaneously — each with its own token, configuration, and logic.

This is ideal for projects that require a "User Bot" and an "Admin Bot" with different command sets.

**Note on Routing**: The default `WebhookHandler` is designed for standalone scripts.
If your bot lives inside a modern PHP framework or handles multiple bot identities, simply implement
WebhookHandlerInterface.
This allows you to pull the Telegram update from a PSR-7 Request, a Laravel Request object, or even a local test file
without changing a single line of your bot's business logic.

```php
// 1. Configure the Support Bot
// support.webhook.php
$supportBot = Bot::create('SUPPORT_TOKEN', ['log_channel' => 'support_logs'])
    ->withServiceProvider(SupportServiceProvider::class)
    ->runFromWebhook();

// 2. Configure the Marketing Bot
// marketing.webhook.php
$marketingBot = Bot::create('MARKETING_TOKEN', ['promo_mode' => true])
    ->withServiceProvider(MarketingServiceProvider::class)
    ->runFromWebhook();
```