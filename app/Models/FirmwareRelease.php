<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FirmwareRelease extends Model
{
    use HasFactory;

    protected $fillable = [
        'version',
        'release_title',
        'changelog',
        'file_path',
        'file_size_bytes',
        'checksum_sha256',
        'is_latest',
        'is_stable',
        'min_hardware_version',
        'created_by',
    ];

    protected $casts = [
        'is_latest' => 'boolean',
        'is_stable' => 'boolean',
        'file_size_bytes' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
