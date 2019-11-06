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

    public function create($name,$email,$password){
        $this->name = $name;
        $this->email = $email;
        $this->password = password_hash($password,PASSWORD_BCRYPT);
        return $this->save();
    }

    public function scopeAuth($query,$email,$password){
        $result = $query->where('email',$email)->first();
        if(password_verify($password,$result->password)){
            return true;
        }
        return false;
    }

}
