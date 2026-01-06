<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;

class GameCategory extends Model
{
    //1. Traits
    use HasUlids;
    use SoftDeletes;
    use HasFactory;

    //2. Mass Assignment
    protected $fillable = [
        'name',
        'slug',
        'description',
        'created_by',
        'updated_by',
    ];

    //3. Relationships
    public function subcategories()
    {
        return $this->hasMany(GameSubcategory::class, 'game_category_id');
    }

    public function games()
    {
        return $this->hasMany(Game::class, 'game_category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

}
