<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SystemAlert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    /**
     * Get system alerts.
     */
    public function index(Request $request): JsonResponse
    {
        $unreadOnly = filter_var($request->query('unreadOnly', false), FILTER_VALIDATE_BOOLEAN);

        $query = SystemAlert::orderBy('created_at', 'desc');

        if ($unreadOnly) {
            $query->where('is_read', false);
        }

        $alerts = $query->limit(50)->get()->map(function ($a) {
            $type = match($a->level) {
                'CRITICAL', 'EMERGENCY' => 'critical',
                'WARNING' => 'warning',
                'SUCCESS' => 'success',
                default => 'info',
            };

            $route = match($a->category) {
                'SENSOR' => 'monitoring',
                'BATCH' => $a->batch_id ? 'history' : 'dashboard',
                'DEVICE' => 'guide',
                'ACTUATOR' => 'monitoring',
                'SYSTEM' => 'settings',
                default => 'monitoring'
            };

            $actionLabel = match($a->category) {
                'SENSOR' => 'Lihat Monitoring',
                'BATCH' => 'Lihat Riwayat',
                'DEVICE' => 'Periksa Alat',
                'ACTUATOR' => 'Kontrol Aktuator',
                'SYSTEM' => 'Pengaturan',
                default => 'Lihat Detail'
            };

            $actionLabelEn = match($a->category) {
                'SENSOR' => 'Live Monitoring',
                'BATCH' => 'View History',
                'DEVICE' => 'Check Hardware',
                'ACTUATOR' => 'Actuator Control',
                'SYSTEM' => 'Settings',
                default => 'View Details'
            };

            return [
                'id' => $a->id,
                'batchId' => $a->batch_id,
                'level' => $a->level,
                'type' => $type,
                'category' => $a->category,
                'title' => $a->title,
                'titleEn' => $a->title,
                'message' => $a->message,
                'messageEn' => $a->message,
                'isRead' => (bool) $a->is_read,
                'unread' => !(bool) $a->is_read,
                'createdAt' => $a->created_at->toIso8601String(),
                'time' => $a->created_at->locale('id')->diffForHumans(),
                'timeEn' => $a->created_at->locale('en')->diffForHumans(),
                'route' => $route,
                'actionLabel' => $actionLabel,
                'actionLabelEn' => $actionLabelEn,
            ];
        });

        return response()->json($alerts);
    }

    /**
     * Mark single alert as read.
     */
    public function markAsRead(string $id): JsonResponse
    {
        $alert = SystemAlert::findOrFail($id);
        $alert->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all alerts as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        SystemAlert::where('is_read', false)->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete a single alert.
     */
    public function destroy(string $id): JsonResponse
    {
        $alert = SystemAlert::findOrFail($id);
        $alert->delete();

        return response()->json(['success' => true, 'message' => 'Notifikasi berhasil dihapus.']);
    }

    /**
     * Clear all alerts.
     */
    public function clear(): JsonResponse
    {
        SystemAlert::truncate();

        return response()->json(['success' => true, 'message' => 'Seluruh notifikasi telah dibersihkan.']);
    }
}
