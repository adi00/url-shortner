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
    function index(){
        $authUser= Auth::user();
       // echo "<pre>";
       if($authUser->isSuperAdmin()){
         $companies= Company::withCount(['users','shortUrls'])->with('users')->paginate();
         $allCompanies = Company::all();
         $shortUrls= ShortUrl::with(['user','company'])->latest()->paginate(10);
         return view('dashboard',compact('shortUrls','authUser','allCompanies','companies'));
       }
       if($authUser->isAdmin()){
              $shortUrls = ShortUrl::with('user')
                ->where('company_id', $authUser->company_id)
                ->latest()
                ->paginate(5);

            $teamMembers = User::withCount('shortUrls')
                ->where('company_id', $authUser->company_id)
                ->where('id', '!=', $authUser->id)
                ->paginate(5);

            return view('dashboard', compact('authUser', 'shortUrls', 'teamMembers'));
       }
        $shortUrls= ShortUrl::where('user_id',$authUser->id)->latest()->paginate(5);
        return view('dashboard',compact('shortUrls','authUser'));
    }


    
}
