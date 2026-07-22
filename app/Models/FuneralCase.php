<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FuneralCase extends Model
{
    protected $fillable = [
        'case_number',
        'case_type',
        'deceased_name',
        'family_contact_name',
        'family_contact_phone',
        'location',
        'route_from',
        'route_to',
        'funeral_at',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'funeral_at' => 'datetime',
        ];
    }

    // ── Relations ───────────────────────────────────────────────────────

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function caseDepartments(): HasMany
    {
        return $this->hasMany(CaseDepartment::class, 'case_id');
    }

    // ── Accessors ────────────────────────────────────────────────────────

    /**
     * Ukupan napredak slucaja — prosek tri odeljenja (nikad rucno).
     */
    public function getOverallProgressAttribute(): int
    {
        $depts = $this->caseDepartments;
        if ($depts->isEmpty()) {
            return 0;
        }

        return (int) round($depts->avg('progress'));
    }

    /**
     * Prikaz lokacije/relacije u zavisnosti od tipa slucaja.
     */
    public function getDisplayLocationAttribute(): string
    {
        if ($this->case_type === 'ino') {
            return trim(($this->route_from ?? '') . ' → ' . ($this->route_to ?? ''));
        }

        return $this->location ?? '';
    }

    // ── Static helpers ───────────────────────────────────────────────────

    /**
     * Generisanje broja slucaja: YYYY-NNN (npr. 2026-001).
     * Poziva se iz Observer-a pri kreiranju.
     */
    public static function generateCaseNumber(): string
    {
        $year  = now()->year;
        $count = static::whereYear('created_at', $year)->count();

        return sprintf('%d-%03d', $year, $count + 1);
    }

    // ── Scopes ───────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'aktivan');
    }

    public function scopeDomaci($query)
    {
        return $query->where('case_type', 'domaci');
    }

    public function scopeIno($query)
    {
        return $query->where('case_type', 'ino');
    }
}
