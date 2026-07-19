<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CustomHelper;
class ShortUrlController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

          $user = Auth::user();
            if($user->isSuperAdmin()){
                $shortUrls= ShortUrl::with(['user','company'])->latest()->paginate();
            }elseif($user->isAdmin()){
                $shortUrls= ShortUrl::with(['user','company'])->where('company_id',$user->company_id)->latest()->paginate();
            }else
            {
                $shortUrls = ShortUrl::with(['user', 'company'])
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(20);
            }
             return view('short-urls.index', compact('shortUrls'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
         if (Auth::user()->isSuperAdmin()) {
            abort(403, 'SuperAdmin cannot create short URLs.');
        }

        return view('short-urls.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $user = Auth::user();
        if($user->isSuperAdmin()){
            abort(403, 'SuperAdmin cannot create short URLs.');
        }
        $postdata = $request->validate([
            'original_url' => ['required', 'url', 'max:2048'],
        ]);
        $code= CustomHelper::generateUniqueCode();
        $shortUrl = ShortUrl::create([
            'company_id'   => $user->company_id,
            'user_id'      => $user->id,
            'original_url' => $postdata['original_url'],
            'code'         => $code,
        ]);

        return redirect()->route('short-urls.index')
            ->with('success', 'Short URL created: ' . url("/s/{$shortUrl->code}"));
    }

    /**
     * Display the specified resource.
     */
    public function show(ShortUrl $shortUrl)
    {
        //

        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShortUrl $shortUrl)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShortUrl $shortUrl)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShortUrl $shortUrl)
    {
        //

        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            abort(403);
        }

        if ($user->isAdmin()) {
            if ($shortUrl->company_id !== $user->company_id) {
                abort(403);
            }
        } else {
          
            if ($shortUrl->user_id !== $user->id) {
                abort(403);
            }
        }

        $shortUrl->delete();

        return back()->with('success', 'Short URL deleted.');
    }

    public function export()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            $shortUrls = ShortUrl::with(['user', 'company'])->latest()->get();
        } elseif ($user->isAdmin()) {
            $shortUrls = ShortUrl::with(['user', 'company'])
                ->where('company_id', $user->company_id)
                ->latest()->get();
        } else {
            $shortUrls = ShortUrl::with(['user', 'company'])
                ->where('user_id', $user->id)
                ->latest()->get();
        }

        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="short_urls.csv"'];

        $callback = function () use ($shortUrls, $user) {
            $handle = fopen('php://output', 'w');
            $cols = ['Short URL', 'Original URL', 'Created By', 'Created At'];
            if ($user->isSuperAdmin()) $cols[] = 'Company';
            fputcsv($handle, $cols);

            foreach ($shortUrls as $url) {
                $row = [
                    url("/s/{$url->code}"),
                    $url->original_url,
                    $url->user->name,
                    $url->created_at->format('Y-m-d H:i:s'),
                ];
                if ($user->isSuperAdmin()) $row[] = $url->company->name;
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
