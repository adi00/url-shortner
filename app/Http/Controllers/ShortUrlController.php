<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CustomHelper;
class ShortUrlController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //

          $user = Auth::user();
            if($user->isSuperAdmin()){
                $query= ShortUrl::with(['user','company']);
                if($request->filled('filter_company')){
                    $query->where('company_id', $request->filter_company);
                }
                $query = $this->applyDateFilter($query, $request->filter_period);
                $shortUrls    = $query->latest()->paginate(20)->withQueryString();
                $allCompanies = Company::orderBy('name')->get();
               return view('short-urls.index', compact('shortUrls', 'allCompanies'));     

            }
            
            if($user->isAdmin()){
                $query= ShortUrl::with(['user','company'])->where('company_id',$user->company_id);
                 if ($request->filled('filter_member')) {
                $query->where('user_id', $request->filter_member);
            }
            $query = $this->applyDateFilter($query, $request->filter_period);

            $shortUrls  = $query->latest()->paginate(20)->withQueryString();

            $allMembers = User::where('company_id', $user->company_id)
                ->orderBy('name')->get();

            return view('short-urls.index', compact('shortUrls', 'allMembers'));
            }
            $query = ShortUrl::with(['user', 'company'])
                ->where('user_id', $user->id);
            $query     = $this->applyDateFilter($query, $request->filter_period);
            $shortUrls = $query->latest()->paginate(20)->withQueryString();
             return view('short-urls.index', compact('shortUrls'));
    }

   private function applyDateFilter($query,?string $period){

        return match ($period) {
            'today'      => $query->whereDate('created_at', today()),
            'last_week'  => $query->whereBetween('created_at', [now()->subWeek(), now()]),
            'last_month' => $query->whereBetween('created_at', [now()->subMonth(), now()]),
            'this_month' => $query->whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year),
            default      => $query,

        };
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

    public function export(Request $request)
    {
        $user = Auth::user();

         if ($user->isSuperAdmin()) {
            $query = ShortUrl::with(['user', 'company']);
            if ($request->filled('filter_company')) {
                $query->where('company_id', $request->filter_company);
            }
        } elseif ($user->isAdmin()) {
            $query = ShortUrl::with(['user', 'company'])
                ->where('company_id', $user->company_id);
            if ($request->filled('filter_member')) {
                $query->where('user_id', $request->filter_member);
            }
        } else {
            $query = ShortUrl::with(['user', 'company'])
                ->where('user_id', $user->id);
        }

        $query     = $this->applyDateFilter($query, $request->filter_period);
        $shortUrls = $query->latest()->get();

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
