<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'user';
    public $timestamps = false;

    public function scopeIsExist($query, $email)
    {
        return $query->where('email', $email)->exists();
    }

}
