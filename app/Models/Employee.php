<?php

namespace App\Models;

use Database\Factories\EmployeeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'employee_id',
    'name',
    'email',
    'phone',
    'address',
    'gender',
    'date_of_birth',
    'joining_date',
    'salary',
    'designation',
    'department_id',
    'profile_image',
])]
class Employee extends Model
{
    /** @use HasFactory<EmployeeFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'joining_date' => 'date',
            'salary' => 'decimal:2',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Accessor for profile image public URL.
     */
    public function getProfileImageUrlAttribute(): ?string
    {
        if ($this->profile_image && Storage::disk('public')->exists($this->profile_image)) {
            return asset('storage/' . $this->profile_image);
        }

        return null;
    }

    /**
     * Find attendance for a given date from loaded relationship.
     */
    public function attendanceForDate(string $date): ?Attendance
    {
        return $this->attendances->first(function ($attendance) use ($date) {
            $attDate = $attendance->attendance_date instanceof \Carbon\Carbon
                ? $attendance->attendance_date->toDateString()
                : substr((string) $attendance->attendance_date, 0, 10);

            return $attDate === $date;
        });
    }
}
