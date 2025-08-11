<?php

namespace App\Modules\ActivityLog\Services;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ActivityLogService
{
    /**
     * Get activity logs with filters
     */
    public function getActivities($filters = [])
    {
        $query = Activity::with(['causer', 'subject'])->latest();

        if (isset($filters['user_id'])) {
            $query->where('causer_id', $filters['user_id']);
        }

        if (isset($filters['log_name'])) {
            $query->where('log_name', $filters['log_name']);
        }

        if (isset($filters['subject_type'])) {
            $query->where('subject_type', $filters['subject_type']);
        }

        if (isset($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('properties', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Get activity statistics
     */
    public function getStatistics()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'total' => Activity::count(),
            'today' => Activity::whereDate('created_at', $today)->count(),
            'this_week' => Activity::where('created_at', '>=', $thisWeek)->count(),
            'this_month' => Activity::where('created_at', '>=', $thisMonth)->count(),
            'by_log' => Activity::selectRaw('log_name, COUNT(*) as count')
                ->groupBy('log_name')
                ->pluck('count', 'log_name'),
            'by_user' => Activity::selectRaw('causer_id, COUNT(*) as count')
                ->whereNotNull('causer_id')
                ->groupBy('causer_id')
                ->with('causer')
                ->limit(10)
                ->get(),
            'recent' => Activity::with(['causer', 'subject'])
                ->latest()
                ->limit(10)
                ->get()
        ];
    }

    /**
     * Clean old logs
     */
    public function cleanOldLogs($days = 30)
    {
        $date = Carbon::now()->subDays($days);
        return Activity::where('created_at', '<', $date)->delete();
    }

    /**
     * Export logs to CSV
     */
    public function exportToCsv($activities)
    {
        $csv = [];
        $csv[] = ['ID', 'Log Name', 'Description', 'Subject Type', 'Subject ID', 'User', 'IP', 'Date'];

        foreach ($activities as $activity) {
            $csv[] = [
                $activity->id,
                $activity->log_name,
                $activity->description,
                $activity->subject_type ? class_basename($activity->subject_type) : '',
                $activity->subject_id,
                $activity->causer ? $activity->causer->name : 'System',
                $activity->properties['ip'] ?? '',
                $activity->created_at->format('Y-m-d H:i:s')
            ];
        }

        return $csv;
    }

    /**
     * Log custom activity
     */
    public function log($description, $subject = null, $properties = [])
    {
        $activity = activity();

        if ($subject) {
            $activity->performedOn($subject);
        }

        if (auth()->check()) {
            $activity->causedBy(auth()->user());
        }

        if (!empty($properties)) {
            $activity->withProperties($properties);
        }

        return $activity->log($description);
    }

    /**
     * Get distinct log names
     */
    public function getLogNames()
    {
        return Activity::distinct()->pluck('log_name');
    }

    /**
     * Get distinct subject types
     */
    public function getSubjectTypes()
    {
        return Activity::distinct()
            ->pluck('subject_type')
            ->filter()
            ->map(function($type) {
                return [
                    'value' => $type,
                    'label' => class_basename($type)
                ];
            });
    }
}
