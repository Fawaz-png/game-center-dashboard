<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    //Traits
    use HasUlids;
    use SoftDeletes;
    use HasFactory;
    use Notifiable;

    //Mass Assignment
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'username',
        'phone_number',
        'password',
        'is_active',
    ];

    //Castings
    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    //Relationships
    public function roles()
    {
        return $this->belongsToMany(Role::class)
                    ->using(RoleUser::class)
                    ->wherePivotNull('deleted_at')
                    ->withTimestamps()
                    ->withPivot(['created_by', 'updated_by', 'deleted_at']);
    }
}
