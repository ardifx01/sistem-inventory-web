<?php

use App\Models\ActivityLog;

if (!function_exists('activity_log')) {
    function activity_log($activity)
    {
        ActivityLog::create([
            'user_id'   => auth()->id(),
            'activity'  => $activity,
            'user_agent'=> request()->header('User-Agent'),
        ]);
    }
}
