<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemAlert extends Model
{
    use HasFactory;

    protected $table = 'system_alerts';

    protected $fillable = [
        'batch_id',
        'level',
        'category',
        'title',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    protected static function booted(): void
    {
        static::created(function (SystemAlert $alert) {
            // 1. Broadcast WebSockets Event (Laravel Reverb)
            try {
                event(new \App\Events\AlertTriggered($alert));
            } catch (\Throwable $e) {
                // Silently continue if websockets uninitialized
            }

            // 2. Dispatch Multi-Channel Notifications (Email, Telegram Bot, WhatsApp Gateway)
            \App\Services\NotificationDispatchService::dispatchSystemAlert($alert);
        });
    }
}
