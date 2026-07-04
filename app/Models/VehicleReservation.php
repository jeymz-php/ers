<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class VehicleReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'requester_type',
        'origin_campus_id',
        'purpose',
        'other_purpose',
        'destination_type',
        'destination_campus_id',
        'destination_location',
        'trip_date',
        'trip_dates',
        'pickup_time',
        'notes',
        'attachments',
        'status',
        'remarks',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'trip_date' => 'date',
        'trip_dates' => 'array',
        'attachments' => 'array',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function originCampus()
    {
        return $this->belongsTo(Campus::class, 'origin_campus_id');
    }

    public function destinationCampus()
    {
        return $this->belongsTo(Campus::class, 'destination_campus_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getReservationCodeAttribute()
    {
        $number = $this->id ? str_pad($this->id, 5, '0', STR_PAD_LEFT) : strtoupper(substr(md5(now()->timestamp), 0, 6));
        return "VEH-$number";
    }

    public function getPurposeLabelAttribute()
    {
        if ($this->purpose === 'other') {
            return $this->other_purpose ?: 'Other';
        }

        return $this->purpose === 'transporting' ? 'Transporting' : 'Items Delivery';
    }

    public function getRequesterTypeLabelAttribute()
    {
        return match ($this->requester_type) {
            'student' => 'Student',
            'professor' => 'Professor',
            'admin' => 'Administrator',
            default => ucfirst($this->requester_type),
        };
    }

    public function getDestinationLabelAttribute()
    {
        if ($this->destination_type === 'campus') {
            return $this->destinationCampus?->name ?? 'N/A';
        }

        return $this->destination_location ?: 'N/A';
    }

    /**
     * Normalize a raw list of dates (e.g. straight from a form submission) into
     * a clean, sorted, de-duplicated array of "Y-m-d" strings. Always
     * guarantees the given fallback date is present if nothing else parses.
     *
     * This is the ONLY place trip dates should be prepared before saving —
     * always write the *entire* result to the dedicated `trip_dates` column.
     */
    public static function normalizeTripDates($dates, $fallbackDate = null): array
    {
        $normalized = [];

        if (is_array($dates)) {
            foreach ($dates as $date) {
                if (empty($date)) {
                    continue;
                }

                try {
                    $normalized[] = Carbon::parse($date)->format('Y-m-d');
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        if (empty($normalized) && $fallbackDate) {
            try {
                $normalized[] = Carbon::parse($fallbackDate)->format('Y-m-d');
            } catch (\Exception $e) {
                // ignore unparsable fallback
            }
        }

        $normalized = array_values(array_unique($normalized));
        sort($normalized);

        return $normalized;
    }

    /**
     * Check which of the given candidate dates are already taken by an
     * approved pickup vehicle reservation.
     *
     * @param  array  $selectedDates
     * @param  \App\Models\VehicleReservation[]|iterable  $existingReservations
     */
    public static function getConflictingTripDates(array $selectedDates, iterable $existingReservations): array
    {
        $conflicts = [];

        foreach ($selectedDates as $selectedDate) {
            foreach ($existingReservations as $reservation) {
                $tripDates = is_object($reservation) ? ($reservation->trip_dates ?? []) : [];

                if (in_array($selectedDate, $tripDates, true)) {
                    $conflicts[] = $selectedDate;
                    break;
                }
            }
        }

        return array_values(array_unique($conflicts));
    }

    public function getTripDatesDisplayAttribute(): string
    {
        $dates = $this->trip_dates ?: ($this->trip_date ? [$this->trip_date->format('Y-m-d')] : []);

        if (empty($dates)) {
            return 'N/A';
        }

        if (count($dates) === 1) {
            return Carbon::parse($dates[0])->format('F d, Y');
        }

        return implode(', ', array_map(function ($date) {
            return Carbon::parse($date)->format('F d, Y');
        }, $dates));
    }

    public function getIsMultiDateAttribute(): bool
    {
        return count($this->trip_dates ?? []) > 1;
    }
}