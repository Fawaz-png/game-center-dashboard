<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    //Traits
    use HasUlids;
    use SoftDeletes;

    //Mass Assignment
    protected $fillable = [
        'name',
        'description',
    ];

    //Relationships
    public function users(){
        return $this->belongsToMany(User::class)
                    ->using(RoleUser::class)
                    ->wherePivotNull('deleted_at')
                    ->withTimestamps()
                    ->withPivot(['created_by', 'updated_by', 'deleted_at']);
    }
}
