<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Game extends Model
{
    //1.Traits
    use HasUlids;
    use SoftDeletes;
    use HasFactory;

    //2. Mass Assignment
    protected $fillable = [
        'game_category_id',
        'game_subcategory_id',
        'name',
        'slug',
        'description',
        'image_path',
        'is_active',
        'min_players',
        'max_players',
        'created_by',
        'updated_by',
    ];

    //3. Castings
    protected $casts = [
        'is_active' => 'boolean',
        'min_players' => 'integer',
        'max_players' => 'integer',
    ];

    //4. Relationships
    public function category()
    {
        return $this->belongsTo(GameCategory::class, 'game_category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(GameSubcategory::class, 'game_subcategory_id');
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
