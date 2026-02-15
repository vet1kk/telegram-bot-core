# 🌟 Lightweight PHP Telegram Framework

A modern, type-safe, and highly structured Telegram Bot framework for PHP 8.3+. This framework leverages **PSR-11
Dependency Injection**, **Middleware Pipelines**, and **Attribute-based Routing** to provide a clean development
experience.
---

## Key Features

* **DTO Architecture**: Every update is transformed into a structured object graph (e.g., `Update` -> `Message` ->
  `Chat`) via Reflection.
* **Polymorphic Context**: Retrieve `chat_id` or `user_id` consistently across different update types using the
  `getChatId()` or `getUserId()` contract.
* **Attribute Routing**: Register Commands and Actions directly on classes using native PHP attributes.
* **Event System**: Emit and listen to custom events for decoupled side effects like logging or analytics.
* **2D Keyboard Panel**: Build complex button grids with automatic validation and recursive JSON serialization.
* **Middleware Stack**: Intercept and process updates globally for maintenance mode, logging, or authentication.

---

## Getting Started

### 1. Register and Initialize the Bot

The `Bot` class is the entry point. It initializes the `DI\Container` and registers core services via the
`CoreServiceProvider`.

```php
use Bot\Bot;

$bot = Bot::create('YOUR_TELEGRAM_TOKEN', [
    // Any optional configuration settings, can be accessed via the ConfigService later in your commands or actions etc.
    'maintenance.enabled' => false,
]);
```

---

## Webhook Management

The framework provides a streamlined way to both register your bot with Telegram and handle incoming updates through a
secure execution pipeline.

### 1. Webhook Registration

To receive updates, you must first register your server's URL with the Telegram API. The `Bot` class provides a wrapper
for this using the internal `Client`.

```php
$bot = \Bot\Bot::create($token, $options);

// Register the URL where your bot is hosted
$bot->registerWebhook('https://your-domain.com/webhook.php');
```

#### **Tip**: You typically only run the

`registerWebhook` script once (e.g., via CLI) to link your bot to your URL. Do not include it in your production
`webhook.php` file.

### 2. Webhook Execution Lifecycle

#### The `runFromWebhook` method manages the complete lifecycle of an incoming request:

* **Security & Ingestion**: Safely captures the raw `php://input` stream.
* **JSON Decoding**: Validates the payload structure and logs errors via PSR-3 if decoding fails.
* **Factory Transformation**: Converts the array into a specific, type-casted `UpdateDTO`.
* **Middleware Processing**: Passes the DTO through your registered middleware stack.
* **Final Routing**: Delivers the update to the appropriate Command or Action handler

```php
// In your webhook.php
require __DIR__ . '/../vendor/autoload.php';

use App\Actions\YourAction;
use App\Commands\YourCommand;
use App\Middleware\YourMiddleware;
use App\Listeners\YourListener;
use App\Providers\YourServiceProvider;
use Bot\Bot;

// 1. Initialize the Bot
$bot = Bot::create('YOUR_TELEGRAM_TOKEN', $options)
          ->withMiddleware(YourMiddleware::class)
          ->withCommand(YourCommand::class)
          ->withAction(YourAction::class)
          ->withListener(YourListener::class)
          ->withServiceProvider(YourServiceProvider::class);
          
// 2. Run the Webhook Runner
// This captures php://input, transforms it into a DTO, and routes it.
$bot->runFromWebhook();
```

--- 

## How to Add a Command

Commands handle text messages starting with `/`. Define them by implementing `CommandInterface` and adding the
`#[Command]`
attribute.

```php
namespace App\Commands;

use Bot\Attribute\Command;
use Bot\Http\Client;
use Bot\Command\CommandInterface;
use Bot\DTO\Update\MessageUpdateDTO;

#[Command(name: 'finish')]
class FinishCommand implements CommandInterface 
{
    public function __construct(
        // Inject any dependencies here, e.g. a service or repository that you want to use in the command.
        // The container will automatically resolve it when the command is executed.
        // (Don't forget to register the service in your service provider)
        protected Client $client
    ) {}

    public function handle(MessageUpdateDTO $update): void 
    {
        $this->client->sendMessage($update->getChatId(), 'See you later!');
    }
}

// Register in your bootstrap
$bot->withCommand(FinishCommand::class);
```

---

## Building Keyboards

The framework uses a 2D `panel` grid. Buttons are validated by required fields before being added to the layout to
ensure
`API` compliance.

```php
use Bot\DTO\Keyboard\InlineKeyboardDTO;
use Bot\DTO\Keyboard\Buttons\InlineButtonDTO;

// Create buttons as DTOs using fromArray
$confirmBtn = InlineButtonDTO::fromArray([
    'text' => 'Confirm', 
    'callback_data' => 'ok'
]);

// Build the keyboard grid
$keyboard = InlineKeyboardDTO::fromArray([
    'buttons' => [
        [$confirmBtn] // Adds a row with one button
    ]
]);

// Send via the client (which handles DTO serialization)
$client->sendMessage($chatId, "Execute action?", [
    'reply_markup' => $keyboard
]);
```

---

## How to Add an Action

Actions handle `callback_query` data from inline buttons. Use the `#[Action]` attribute to map the data string to a
specific class.

```php
namespace App\Actions;

use Bot\Action\ActionInterface;
use Bot\Attribute\Action;
use Bot\DTO\Update\CallbackQueryUpdateDTO;
use Bot\Http\Client;

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
        // 1. Create buttons as DTOs
        $profileBtn = InlineButtonDTO::fromArray(['text' => '👤 Profile', 'callback_data' => 'user_profile']);
        $settingsBtn = InlineButtonDTO::fromArray(['text' => '⚙️ Settings', 'callback_data' => 'settings_menu']);
        $helpBtn = InlineButtonDTO::fromArray(['text' => '❓ Help', 'callback_data' => 'help_info']);

        // 2. Arrange buttons in a 2D array for the keyboard
        $keyboard = InlineKeyboardDTO::fromArray([
            'buttons' => [
                [$profileBtn, $settingsBtn],
                [$helpBtn]
            ]
        ]);

        // 3. Send the message with the keyboard
        $this->client->sendMessage($update->getChatId(), "Please choose an option:", [
            'reply_markup' => $keyboard
        ]);
    }
}

// Register in your bootstrap
$bot->withAction(MenuAction::class);
```

---

## How to Add an Middleware

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

## Event Management

The framework includes a powerful `EventManager` that allows for complete decoupling of side effects (like logging,
statistics, or external notifications) from your core command and action logic.

### 1. Registering a Listener

Listeners are registered using the `withListener` method. The framework uses Reflection to scan for the `#[Listener]`
attribute on class methods to map them to specific event classes.

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

### 2. Build-in Events

The framework emits several internal events that you can hook into:

* `CommandHandledEvent`: Emitted after a command has been successfully handled.
* `ActionHandledEvent`: Emitted after an action has been successfully handled.
* `ReceivedEvent`: Emitted immediately after an update is received and transformed into a DTO
* `UnhandledEvent`: Emitted when an update does not match any registered command or action, allowing you to implement
  fallback logic.

### 3. Emitting Custom Events

You can also emit your own events from anywhere within your application by retrieving the `EventManager` from the
`container`.

```php
use Bot\Event\EventManager;

$eventManager = $container->get(EventManager::class);
$eventManager->emit(new MyCustomEvent($data));
```

---

## Dependency Injection & Service Providers

The framework uses a PSR-11 compliant container. While core services are loaded via the `CoreServiceProvider`, you can
register your own custom services to be injected into your Commands, Actions, or Middlewares.

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
        $container->set(AnalyticsService::class => \DI\autowire(AnalyticsService::class))
    }
}
```
