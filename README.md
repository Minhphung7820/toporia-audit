# Toporia Audit

Audit logging for Toporia Framework.

## Installation

```bash
composer require toporia/audit
```

## Auto-Discovery

This package uses Toporia's **Package Auto-Discovery** system. After installation:

- **Service Provider** is automatically registered - no manual registration required
- **Configuration** is automatically discovered from `extra.toporia.config` in composer.json
- **Migrations** are automatically included when running `php console migrate`

To rebuild the package manifest manually:

```bash
php console package:discover
```

## Setup

### 1. Run Migrations

```bash
php console migrate
```

Package migrations are automatically discovered and included.
To skip package migrations:

```bash
php console migrate --no-packages
```

### 2. Publish Config (optional)

```bash
php console vendor:publish --provider="Toporia\Audit\AuditServiceProvider"
# Or with tag
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
