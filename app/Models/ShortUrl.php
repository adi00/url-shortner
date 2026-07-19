<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class ShortUrl extends Model
{
    //
     use HasFactory;
     protected $fillable = [
        'company_id',
        'user_id',
        'original_url',
        'code',
    ];

    function company(){
        return $this->belongsTo(Company::class);
    }

    function user(){
        return $this->belongsTo(User::class);
    }
}
