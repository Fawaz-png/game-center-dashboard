<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleUser extends Pivot
{
    //Traits
    use SoftDeletes;
    public $incrementing = false; // Disable auto-incrementing since we are using composite keys
    //Table Name
    protected $table = 'role_user';
    public $timestamps = true; // Enable timestamps

}
