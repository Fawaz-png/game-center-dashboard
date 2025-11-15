<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GameVersion extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $table = 'game_versions';

    protected $fillable = [
        'game_id',
        'version_name',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'metadata' => 'json',
    ];

    /**
     * The game this version belongs to.
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
