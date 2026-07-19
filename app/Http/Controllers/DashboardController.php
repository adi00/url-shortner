<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShortUrl;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
class DashboardController extends Controller
{
    //
    function index(Request $request){
        $authUser= Auth::user();
       // echo "<pre>";
       if($authUser->isSuperAdmin()){
         $companies = Company::withCount(['users', 'shortUrls'])
                ->with(['users', 'shortUrls'])
                ->paginate(10);
         $allCompanies = Company::orderBy('name')->get();
         $query = ShortUrl::with(['user', 'company']);
         if ($request->filled('filter_company')) {
                $query->where('company_id', $request->filter_company);
            }
        $query = $this->applyDateFilter($query, $request->filter_period);
         $shortUrls = $query->latest()->paginate(10)->withQueryString();
         return view('dashboard',compact('shortUrls','authUser','allCompanies','companies'));
       }
       if($authUser->isAdmin()){
              $query = ShortUrl::with('user')
                ->where('company_id', $authUser->company_id);

            if ($request->filled('filter_member')) {
                $query->where('user_id', $request->filter_member);
            }

            $query = $this->applyDateFilter($query, $request->filter_period);

            $shortUrls = $query->latest()->paginate(5)->withQueryString();

            $teamMembers = User::withCount('shortUrls')
                ->with('shortUrls')
                ->where('company_id', $authUser->company_id)
                ->where('id', '!=', $authUser->id)
                ->paginate(5);


            $allMembers = User::where('company_id', $authUser->company_id)
                ->where('id', '!=', $authUser->id)
                ->orderBy('name')
                ->get();

            return view('dashboard', compact('authUser', 'shortUrls', 'teamMembers', 'allMembers'));
       }

        $query= ShortUrl::where('user_id',$authUser->id);
        $query = $this->applyDateFilter($query, $request->filter_period);
        $shortUrls = $query->latest()->paginate(5)->withQueryString();
        return view('dashboard',compact('shortUrls','authUser'));
    }

    private function applyDateFilter($query, ?string $period)
    {
        return match ($period) {
            'today'      => $query->whereDate('created_at', today()),
            'last_week'  => $query->whereBetween('created_at', [now()->subWeek(), now()]),
            'last_month' => $query->whereBetween('created_at', [now()->subMonth(), now()]),
            'this_month' => $query->whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year),
            default      => $query,
        };
    }

    
}
