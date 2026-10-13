<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipe extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(RecipeImage::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(Step::class)->orderBy('order');
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class)
            ->withPivot(['quantity', 'unit'])
            ->withTimestamps();
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function defaultImage(): HasOne
    {
        return $this->hasOne(RecipeImage::class)->where('is_default', true);
    }

    public function getFormattedTimeAttribute(): string
    {
        $hours = intdiv($this->cooking_time, 60);
        $minutes = $this->cooking_time % 60;

        $parts = [];

        if ($hours > 0) {
            $parts[] = $hours . ' ' . $this->plural($hours, 'час', 'часа', 'часов');
        }

        if ($minutes > 0 || $hours === 0) {
            $parts[] = $minutes . ' ' . $this->plural($minutes, 'минута', 'минуты', 'минут');
        }

        return implode(' ', $parts);
    }

    private function plural(int $number, string $one, string $few, string $many): string
    {
        $mod10 = $number % 10;
        $mod100 = $number % 100;

        if ($mod10 === 1 && $mod100 !== 11) {
            return $one;
        }

        if ($mod10 >= 2 && $mod10 <= 4 && ($mod100 < 10 || $mod100 >= 20)) {
            return $few;
        }

        return $many;
    }
}
