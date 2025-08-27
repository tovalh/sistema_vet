<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    use HasFactory, BelongsToTenant, BelongsToBranch;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'name',
        'species',
        'breed',
        'gender',
        'birth_date',
        'weight',
        'color',
        'microchip',
        'owner_name',
        'owner_phone',
        'owner_email',
        'owner_address',
        'medical_notes',
        'is_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function getAgeAttribute(): ?string
    {
        if (!$this->birth_date) {
            return null;
        }
        
        $age = $this->birth_date->diffInYears(now());
        $months = $this->birth_date->diffInMonths(now()) % 12;
        
        if ($age === 0) {
            return "{$months} meses";
        }
        
        return $months > 0 ? "{$age} años, {$months} meses" : "{$age} años";
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
