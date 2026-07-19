<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShortUrl;
class RedirectShortUrlController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(string $code)
    {
        //
        $shortUrl = ShortUrl::where('code', $code)->firstOrFail();
        $shortUrl->increment('hits');
        return redirect()->away($shortUrl->original_url);
    }
}
