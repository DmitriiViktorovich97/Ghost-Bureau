<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = [
        "name",
        "type",
        "capacity",
        "lighting",
        "noise_level",
        "humidity",
        "has_people",
        "has_attic",
        "has_mirrors",
        "description",
    ];
    protected $casts = [
        "has_people" => "boolean",
        "has_attic" => "boolean",
        "has_mirrors" => "boolean",
    ];

    public function ghosts(): HasMany
    {
        return $this->hasMany(Ghost::class);
    }

    public function occupied(): int
    {
        return $this->ghosts()->count();
    }

    public function free(): int
    {
        return max(0, $this->capacity - $this->occupied());
    }

    public function isFull(): bool
    {
        return $this->free() <= 0;
    }
}
