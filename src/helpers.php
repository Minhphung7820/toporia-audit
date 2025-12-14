<?php

declare(strict_types=1);

use Toporia\Audit\AuditManager;
use Toporia\Audit\Contracts\AuditableInterface;

if (!function_exists('audit')) {
    /**
     * Get the audit manager instance.
     *
     * @return AuditManager
     */
    function audit(): AuditManager
    {
        static $manager = null;

        if ($manager === null) {
            if (function_exists('app') && app()->has(AuditManager::class)) {
                $manager = app()->make(AuditManager::class);
            } else {
                $config = function_exists('config') ? config('audit', []) : [];
                $manager = new AuditManager($config);
            }
        }

        return $manager;
    }
}

if (!function_exists('audit_record')) {
    /**
     * Record a custom audit entry.
     *
     * @param AuditableInterface $model
     * @param string $event
     * @param array<string, mixed> $metadata
     * @return void
     */
    function audit_record(AuditableInterface $model, string $event, array $metadata = []): void
    {
        audit()->recordCustom($model, $event, $metadata);
    }
}

if (!function_exists('without_auditing')) {
    /**
     * Execute callback without auditing.
     *
     * @param callable $callback
     * @return mixed
     */
    function without_auditing(callable $callback): mixed
    {
        $wasEnabled = audit()->isEnabled();
        audit()->disable();

        try {
            return $callback();
        } finally {
            if ($wasEnabled) {
                audit()->enable();
            }
        }
    }
}
