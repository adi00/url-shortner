<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    //

    protected $fillable= [
        'company_id',
        'invited_by',
        'email',
        'role',
        'token',
        'accepted_at',
    ];

    protected $cast=[
        'accepted_at'=>'datetime'
    ];

    function company(){
        return $this->belongsto(Company::class);
    }
}
