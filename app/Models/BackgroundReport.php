<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BackgroundReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_type',
        'report_name',
        'filters',
        'status',
        'file_path',
        'error_message',
        'requested_by',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'filters' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'badge-warning',
            'processing' => 'badge-info',
            'completed' => 'badge-success',
            'failed' => 'badge-danger',
        ];

        return $badges[$this->status] ?? 'badge-secondary';
    }

    public function getDurationAttribute()
    {
        if (!$this->started_at) {
            return 'Not started';
        }

        $end = $this->completed_at ?? now();
        return $this->started_at->diffForHumans($end, true);
    }

    public function getFileSizeAttribute()
    {
        if (!$this->file_path || !file_exists(storage_path('app/' . $this->file_path))) {
            return null;
        }

        $bytes = filesize(storage_path('app/' . $this->file_path));
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
