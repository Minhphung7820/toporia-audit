# Toporia Audit

Audit logging for Toporia Framework.

## Installation

```bash
composer require toporia/audit
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

    // Optional: exclude fields
    protected static array $auditExclude = ['internal_notes'];
}
```

### 2. Use Middleware (optional)

```php
$router->group(['middleware' => ['audit']], function ($router) {
    // Captures user context for audit logs
});
```

### 3. Query Audit History

```php
$history = audit()->getHistory(Order::class, $orderId);

foreach ($history as $entry) {
    echo "{$entry->userName} {$entry->event} at {$entry->timestamp}";
}
```

### 4. Helper Functions

```php
audit();                    // Get AuditManager
audit_record($model, 'exported', $metadata);  // Custom audit
without_auditing(fn() => ...);  // Disable audit temporarily
```

## Configuration

Publish config:
```bash
php console vendor:publish --tag=audit-config
```

## License

MIT
