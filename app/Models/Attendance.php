<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\AttendanceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'employee_id',
    'attendance_date',
    'status',
    'check_in',
    'check_out',
    'remarks',
])]
class Attendance extends Model
{
    /** @use HasFactory<AttendanceFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * 12-hour formatted Check In time (e.g. 09:00 AM).
     */
    public function getFormattedCheckInAttribute(): string
    {
        if (! $this->check_in) {
            return '—';
        }

        try {
            return Carbon::parse($this->check_in)->format('h:i A');
        } catch (\Exception $e) {
            return (string) $this->check_in;
        }
    }

    /**
     * 12-hour formatted Check Out time (e.g. 05:30 PM).
     */
    public function getFormattedCheckOutAttribute(): string
    {
        if (! $this->check_out) {
            return '—';
        }

        try {
            return Carbon::parse($this->check_out)->format('h:i A');
        } catch (\Exception $e) {
            return (string) $this->check_out;
        }
    }

    /**
     * Uniform formatted attendance date (e.g. 11-09-2026).
     */
    public function getFormattedDateAttribute(): string
    {
        if (! $this->attendance_date) {
            return '—';
        }

        try {
            return Carbon::parse($this->attendance_date)->format('d-m-Y');
        } catch (\Exception $e) {
            return (string) $this->attendance_date;
        }
    }
}
