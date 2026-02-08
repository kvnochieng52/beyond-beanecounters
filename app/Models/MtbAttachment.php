<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MtbAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'mtb_id',
        'file_name',
        'original_name',
        'file_size',
        'file_type',
        'file_path',
        'created_by'
    ];

    public function mtb()
    {
        return $this->belongsTo(Mtb::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFileUrlAttribute()
    {
        return route('mtb.download-attachment', $this->id);
    }

    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    public function getFileTypeSimpleAttribute()
    {
        $mimeType = $this->file_type;

        // Map of MIME types to simple names
        $mimeMap = [
            // PDF
            'application/pdf' => 'PDF',

            // Word Documents
            'application/msword' => 'Word',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'Word',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.template' => 'Word',

            // Excel
            'application/vnd.ms-excel' => 'Excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'Excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.template' => 'Excel',

            // Images
            'image/jpeg' => 'Image',
            'image/jpg' => 'Image',
            'image/png' => 'Image',
            'image/gif' => 'Image',
            'image/webp' => 'Image',
            'image/bmp' => 'Image',
            'image/svg+xml' => 'Image',

            // Text
            'text/plain' => 'Text',
            'text/csv' => 'CSV',

            // PowerPoint
            'application/vnd.ms-powerpoint' => 'PowerPoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'PowerPoint',
        ];

        return $mimeMap[$mimeType] ?? ucfirst(str_replace('application/', '', $mimeType));
    }
}
