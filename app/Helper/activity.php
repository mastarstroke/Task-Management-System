<?php

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

if (!function_exists('activity_log')) {
    /**
     * Log system activity.
     *
     * @param string $action
     * @param array|null $details
     * @return void
     */
    function activity_log(string $action, ?array $details = null): void
    {
        try {
            ActivityLog::create([
                'user_id' => Auth::user()->id,
                'action' => $action,
                'details' => $details,
                'ip_address' => Request::ip(),
                'user_agent' => Request::header('User-Agent'),
            ]);
        } catch (\Exception $e) {
            \Log::error('Activity log failed: ' . $e->getMessage());
        }
    }
}
