<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Category extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        'name',
        'slug',
        'icon',
    ];
    
    public function produks(): hasMany
    {
        return $this->hasMany(related: Produk::class);
    }

    public function setNameAttribute ($value): void
    {
        $this-> attributes['name'] = $value;
        $this-> attributes['slug'] = Str::slug (title: $value);
    }
}
