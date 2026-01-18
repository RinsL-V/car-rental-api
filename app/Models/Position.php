<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'code'];

    public function carCategories(): BelongsToMany
    {
        return $this->belongsToMany(CarCategory::class, 'position_car_category');
    }

    public function canUseCategory(int $categoryId): bool
    {
        return $this->carCategories()->where('car_categories.id', $categoryId)->exists();
    }
}
