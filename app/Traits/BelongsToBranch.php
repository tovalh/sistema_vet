<?php

namespace App\Traits;

use App\Models\Branch;
use App\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToBranch
{
    protected static function bootBelongsToBranch(): void
    {
        static::addGlobalScope(new BranchScope);

        static::creating(function ($model) {
            if (session()->has('branch_id') && !$model->branch_id) {
                $model->branch_id = session('branch_id');
            }
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}