<?php

declare(strict_types=1);

namespace OrchidHelpers\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuditLogService
{
    public static function make(): static
    {
        return new static();
    }

    /**
     * Log model creation
     *
     * @param  Model  $model
     * @param  array  $changes
     * @param  string|null  $event
     * @param  mixed  $user
     * @return bool
     */
    public function logCreate(Model $model, array $changes = [], ?string $event = null, $user = null): bool
    {
        return $this->log($model, 'create', $changes, $event, $user);
    }

    /**
     * Log model update
     *
     * @param  Model  $model
     * @param  array  $oldValues
     * @param  array  $newValues
     * @param  string|null  $event
     * @param  mixed  $user
     * @return bool
     */
    public function logUpdate(Model $model, array $oldValues, array $newValues, ?string $event = null, $user = null): bool
    {
        $changes = $this->getChanges($oldValues, $newValues);
        return $this->log($model, 'update', $changes, $event, $user);
    }

    /**
     * Log model deletion
     *
     * @param  Model  $model
     * @param  array  $oldValues
     * @param  string|null  $event
     * @param  mixed  $user
     * @return bool
     */
    public function logDelete(Model $model, array $oldValues = [], ?string $event = null, $user = null): bool
    {
        return $this->log($model, 'delete', $oldValues, $event, $user);
    }

    /**
     * Log custom event
     *
     * @param  string  $action
     * @param  string  $modelType
     * @param  mixed  $modelId
     * @param  array  $data
     * @param  string|null  $event
     * @param  mixed  $user
     * @return bool
     */
    public function logCustom(string $action, string $modelType, $modelId, array $data = [], ?string $event = null, $user = null): bool
    {
        $user = $user ?? Auth::user();
        
        try {
            DB::table('audit_logs')->insert([
                'user_id' => $user?->id,
                'user_type' => $user ? get_class($user) : null,
                'action' => $action,
                'event' => $event,
                'auditable_type' => $modelType,
                'auditable_id' => $modelId,
                'old_values' => json_encode([]),
                'new_values' => json_encode($data),
                'url' => request()?->fullUrl(),
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get audit logs for model
     *
     * @param  Model|string  $model
     * @param  mixed  $modelId
     * @param  array  $actions
     * @param  int  $limit
     * @return array
     */
    public function getLogs($model, $modelId = null, array $actions = [], int $limit = 50): array
    {
        $query = DB::table('audit_logs');
        
        if ($model instanceof Model) {
            $query->where('auditable_type', get_class($model))
                  ->where('auditable_id', $model->getKey());
        } elseif (is_string($model) && $modelId) {
            $query->where('auditable_type', $model)
                  ->where('auditable_id', $modelId);
        } elseif (is_string($model)) {
            $query->where('auditable_type', $model);
        }
        
        if (!empty($actions)) {
            $query->whereIn('action', $actions);
        }
        
        return $query->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get()
                    ->toArray();
    }

    /**
     * Get user activity logs
     *
     * @param  mixed  $user
     * @param  array  $actions
     * @param  int  $limit
     * @return array
     */
    public function getUserActivity($user, array $actions = [], int $limit = 50): array
    {
        $userId = $user instanceof Model ? $user->getKey() : $user;
        $userType = $user instanceof Model ? get_class($user) : null;
        
        $query = DB::table('audit_logs')
                   ->where('user_id', $userId);
        
        if ($userType) {
            $query->where('user_type', $userType);
        }
        
        if (!empty($actions)) {
            $query->whereIn('action', $actions);
        }
        
        return $query->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get()
                    ->toArray();
    }

    /**
     * Get recent activity
     *
     * @param  int  $hours
     * @param  array  $actions
     * @param  int  $limit
     * @return array
     */
    public function getRecentActivity(int $hours = 24, array $actions = [], int $limit = 100): array
    {
        $query = DB::table('audit_logs')
                   ->where('created_at', '>=', now()->subHours($hours));
        
        if (!empty($actions)) {
            $query->whereIn('action', $actions);
        }
        
        return $query->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get()
                    ->toArray();
    }

    /**
     * Clean old audit logs
     *
     * @param  int  $days
     * @return int
     */
    public function cleanOldLogs(int $days = 90): int
    {
        return DB::table('audit_logs')
                ->where('created_at', '<', now()->subDays($days))
                ->delete();
    }

    /**
     * Get audit statistics
     *
     * @param  int  $days
     * @return array
     */
    public function getStatistics(int $days = 30): array
    {
        $startDate = now()->subDays($days);
        
        $stats = DB::table('audit_logs')
                  ->select(
                      DB::raw('DATE(created_at) as date'),
                      DB::raw('COUNT(*) as total'),
                      DB::raw('SUM(CASE WHEN action = "create" THEN 1 ELSE 0 END) as creates'),
                      DB::raw('SUM(CASE WHEN action = "update" THEN 1 ELSE 0 END) as updates'),
                      DB::raw('SUM(CASE WHEN action = "delete" THEN 1 ELSE 0 END) as deletes')
                  )
                  ->where('created_at', '>=', $startDate)
                  ->groupBy(DB::raw('DATE(created_at)'))
                  ->orderBy('date', 'desc')
                  ->get()
                  ->toArray();
        
        $topUsers = DB::table('audit_logs')
                    ->select(
                        'user_id',
                        DB::raw('COUNT(*) as activity_count')
                    )
                    ->where('created_at', '>=', $startDate)
                    ->whereNotNull('user_id')
                    ->groupBy('user_id')
                    ->orderBy('activity_count', 'desc')
                    ->limit(10)
                    ->get()
                    ->toArray();
        
        $topModels = DB::table('audit_logs')
                     ->select(
                         'auditable_type',
                         DB::raw('COUNT(*) as activity_count')
                     )
                     ->where('created_at', '>=', $startDate)
                     ->whereNotNull('auditable_type')
                     ->groupBy('auditable_type')
                     ->orderBy('activity_count', 'desc')
                     ->limit(10)
                     ->get()
                     ->toArray();
        
        return [
            'period_days' => $days,
            'total_activities' => array_sum(array_column($stats, 'total')),
            'daily_stats' => $stats,
            'top_users' => $topUsers,
            'top_models' => $topModels,
        ];
    }

    /**
     * Export audit logs
     *
     * @param  array  $filters
     * @param  string  $format
     * @return mixed
     */
    public function exportLogs(array $filters = [], string $format = 'csv')
    {
        $query = DB::table('audit_logs');
        
        $this->applyFilters($query, $filters);
        
        $logs = $query->orderBy('created_at', 'desc')->get();
        
        if ($format === 'csv') {
            return $this->exportToCsv($logs);
        } elseif ($format === 'json') {
            return json_encode($logs, JSON_PRETTY_PRINT);
        }
        
        return $logs;
    }

    /**
     * Log action
     *
     * @param  Model  $model
     * @param  string  $action
     * @param  array  $changes
     * @param  string|null  $event
     * @param  mixed  $user
     * @return bool
     */
    private function log(Model $model, string $action, array $changes = [], ?string $event = null, $user = null): bool
    {
        $user = $user ?? Auth::user();
        
        try {
            DB::table('audit_logs')->insert([
                'user_id' => $user?->id,
                'user_type' => $user ? get_class($user) : null,
                'action' => $action,
                'event' => $event,
                'auditable_type' => get_class($model),
                'auditable_id' => $model->getKey(),
                'old_values' => $action === 'update' ? json_encode($changes['old'] ?? []) : json_encode([]),
                'new_values' => $action === 'update' ? json_encode($changes['new'] ?? []) : json_encode($changes),
                'url' => request()?->fullUrl(),
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get changes between old and new values
     *
     * @param  array  $oldValues
     * @param  array  $newValues
     * @return array
     */
    private function getChanges(array $oldValues, array $newValues): array
    {
        $changes = [];
        
        foreach ($newValues as $key => $newValue) {
            $oldValue = $oldValues[$key] ?? null;
            
            if ($oldValue != $newValue) {
                $changes['old'][$key] = $oldValue;
                $changes['new'][$key] = $newValue;
            }
        }
        
        return $changes;
    }

    /**
     * Apply filters to query
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $filters
     * @return void
     */
    private function applyFilters($query, array $filters): void
    {
        foreach ($filters as $key => $value) {
            if ($value !== null) {
                switch ($key) {
                    case 'user_id':
                        $query->where('user_id', $value);
                        break;
                    case 'action':
                        $query->where('action', $value);
                        break;
                    case 'auditable_type':
                        $query->where('auditable_type', $value);
                        break;
                    case 'start_date':
                        $query->where('created_at', '>=', $value);
                        break;
                    case 'end_date':
                        $query->where('created_at', '<=', $value);
                        break;
                    case 'ip_address':
                        $query->where('ip_address', $value);
                        break;
                }
            }
        }
    }

    /**
     * Export to CSV
     *
     * @param  mixed  $data
     * @return string
     */
    private function exportToCsv($data): string
    {
        $output = fopen('php://temp', 'w');
        
        // Write headers
        fputcsv($output, [
            'ID', 'User ID', 'Action', 'Event', 'Model Type', 'Model ID',
            'Old Values', 'New Values', 'URL', 'IP Address', 'User Agent', 'Created At'
        ]);
        
        // Write data
        foreach ($data as $row) {
            fputcsv($output, [
                $row->id,
                $row->user_id,
                $row->action,
                $row->event,
                $row->auditable_type,
                $row->auditable_id,
                $row->old_values,
                $row->new_values,
                $row->url,
                $row->ip_address,
                $row->user_agent,
                $row->created_at,
            ]);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }

    /**
     * Check if audit logging is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return config('audit.enabled', true);
    }

    /**
     * Get audit log table name
     *
     * @return string
     */
    public function getTableName(): string
    {
        return config('audit.table', 'audit_logs');
    }

    /**
     * Get available actions
     *
     * @return array
     */
    public function getAvailableActions(): array
    {
        return ['create', 'update', 'delete', 'restore', 'force_delete', 'login', 'logout', 'custom'];
    }
}