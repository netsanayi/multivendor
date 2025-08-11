<?php

namespace App\Modules\ActivityLog\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the activity logs.
     */
    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'subject'])
            ->latest();

        // Kullanıcı filtresi
        if ($request->has('user_id')) {
            $query->where('causer_id', $request->get('user_id'));
        }

        // Log adı filtresi
        if ($request->has('log_name')) {
            $query->where('log_name', $request->get('log_name'));
        }

        // Konu tipi filtresi
        if ($request->has('subject_type')) {
            $query->where('subject_type', $request->get('subject_type'));
        }

        // Tarih aralığı filtresi
        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->get('start_date'));
        }

        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->get('end_date'));
        }

        // Arama
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('properties', 'like', "%{$search}%");
            });
        }

        $activities = $query->paginate(50);

        // Filtreleme için veri hazırla
        $logNames = Activity::distinct()->pluck('log_name');
        $subjectTypes = Activity::distinct()->pluck('subject_type')
            ->map(function($type) {
                return [
                    'value' => $type,
                    'label' => class_basename($type)
                ];
            });

        return view('admin.activity-log.index', compact(
            'activities', 
            'logNames', 
            'subjectTypes'
        ));
    }

    /**
     * Display the specified activity log.
     */
    public function show(Activity $activity)
    {
        $activity->load(['causer', 'subject']);
        
        return view('admin.activity-log.show', compact('activity'));
    }

    /**
     * Clear old activity logs.
     */
    public function clear(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1'
        ]);

        $date = now()->subDays($request->days);
        
        $deletedCount = Activity::where('created_at', '<', $date)->delete();

        return redirect()
            ->route('admin.activity-log.index')
            ->with('success', "{$deletedCount} adet eski log kaydı silindi.");
    }

    /**
     * Export activity logs.
     */
    public function export(Request $request)
    {
        $query = Activity::with(['causer', 'subject']);

        // Filtreleri uygula
        if ($request->has('user_id')) {
            $query->where('causer_id', $request->get('user_id'));
        }

        if ($request->has('log_name')) {
            $query->where('log_name', $request->get('log_name'));
        }

        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->get('start_date'));
        }

        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->get('end_date'));
        }

        $activities = $query->get();

        // CSV formatında export
        $filename = 'activity_log_' . now()->format('Y_m_d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($activities) {
            $file = fopen('php://output', 'w');
            
            // Başlıklar
            fputcsv($file, [
                'ID',
                'Log Adı',
                'Açıklama',
                'Konu',
                'Konu ID',
                'Kullanıcı',
                'IP Adresi',
                'User Agent',
                'Özellikler',
                'Tarih'
            ]);

            // Veriler
            foreach ($activities as $activity) {
                fputcsv($file, [
                    $activity->id,
                    $activity->log_name,
                    $activity->description,
                    $activity->subject_type ? class_basename($activity->subject_type) : '',
                    $activity->subject_id,
                    $activity->causer ? $activity->causer->name : '',
                    $activity->properties['ip'] ?? '',
                    $activity->properties['user_agent'] ?? '',
                    json_encode($activity->properties),
                    $activity->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
