<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'comfort_level'];

    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class);
    }
}
