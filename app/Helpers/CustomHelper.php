<?php

namespace App\Helpers;

use App\Models\ShortUrl;
use Illuminate\Support\Str;

class CustomHelper{

    static function generateUniqueCode(int $length=6):string{
       do {
            $code = Str::random($length);
        } while (ShortUrl::where('code', $code)->exists());

        return $code;
    }
}
?>