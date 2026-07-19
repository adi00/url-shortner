<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    //
    use HasFactory;
    protected $fillable= ['name', 'slug'];


    function users(){
        return $this->hasMany(User::class);
    }
    function shortUrls(){
        return $this->hasMany(ShortUrl::class);
    }

    function invitations(){
        return $this->hasMany(Invitation::class);
    }
}
