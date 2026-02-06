<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'title',
        'description',
        'location',
        'appointment_at',
        'resident_name',
        'resident_phone',
        'status',
        'technician_id',
        'completed_at',
        'report_image',
        'completion_image',
    ];

    protected $casts = [
        'appointment_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function workLogs()
    {
        return $this->hasMany(WorkLog::class);
    }
}
