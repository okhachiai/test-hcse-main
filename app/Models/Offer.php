<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    public static $states = [
        'draft' => 'Brouillon',
        'published' => 'Publié',
        'hidden' => 'Masqué',
    ];

    protected $fillable = [
        'name',
        'slug',
        'image',
        'description',
        'state',
    ];

    #[Scope]
    protected function ofState($query, $state)
    {
        return $query->where('state', $state);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
