# Toporia Audit

Audit logging for Toporia Framework.

## Installation

```bash
composer require toporia/audit
```

## Setup

### 1. Register Service Provider

Add to `bootstrap/app.php` or `App/Infrastructure/Providers/AppServiceProvider.php`:

```php
// bootstrap/app.php - trong RegisterProviders::bootstrap()
$app->registerProviders([
    // ... other providers
    \Toporia\Audit\AuditServiceProvider::class,
]);

// Hoặc trong AppServiceProvider
public function register(ContainerInterface $container): void
{
    $container->register(\Toporia\Audit\AuditServiceProvider::class);
}
```

### 2. Run Migration

```bash
php console migrate
```

Migration file: `database/migrations/2025_12_14_000001_CreateAuditLogsTable.php`

### 3. Publish Config (optional)

```bash
php console vendor:publish --tag=audit-config
```

## Usage

### 1. Add Auditable to Models

```php
use Toporia\Audit\Concerns\Auditable;

class Order extends Model
{
    use Auditable;

    // Optional: specify fields to audit
    protected static array $auditInclude = ['status', 'total'];

    // Optional: exclude sensitive fields
    protected static array $auditExclude = ['internal_notes'];
}
```

### 2. Use Middleware (captures user context)

```php
// routes/api.php
$router->group(['middleware' => ['audit']], function ($router) {
    $router->resource('orders', OrderController::class);
});
```

### 3. Query Audit History

```php
$history = audit()->getHistory(Order::class, $orderId);

foreach ($history as $entry) {
    echo "{$entry->userName} {$entry->event} at {$entry->timestamp}\n";
    echo "Old: " . json_encode($entry->oldValues) . "\n";
    echo "New: " . json_encode($entry->newValues) . "\n";
}
```

### 4. Helper Functions

```php
audit();                                    // Get AuditManager
audit_record($model, 'exported', $meta);    // Custom audit entry
without_auditing(fn() => ...);              // Disable audit temporarily
```

## Configuration

```php
// config/audit.php
return [
    'enabled' => true,
    'default' => 'database', // or 'file'

    'drivers' => [
        'database' => [
            'table' => 'audit_logs',
            'batch_size' => 1000,
        ],
        'file' => [
            'path' => storage_path('logs/audit'),
        ],
    ],

    'exclude' => ['password', 'remember_token'],

    'events' => [
        'created' => true,
        'updated' => true,
        'deleted' => true,
    ],
];
```

## License

MIT
