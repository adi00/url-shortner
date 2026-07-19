<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
class InvitationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $user = Auth::user();
        if($user->isMember()){
             abort(403);
        }
        if ($user->isAdmin()) {
            $teamMembers = User::withCount('shortUrls')
                ->where('company_id', $user->company_id)
                ->where('id', '!=', $user->id)
                ->paginate(10);

            return view('invitations.create', compact('teamMembers'));
        }

        return view('invitations.create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
       
         $user = Auth::user();
        if($user->isMember()){
             abort(403);
        }
        if($user->isSuperAdmin()){
             $postdata = $request->validate([
                'email'        => ['required', 'email'],
                'company_name' => ['required', 'string', 'max:255'],
            ]);
            $company= Company::create(['name'=>$postdata['company_name'],'slug'=>Str::slug($postdata['company_name'].'-'.Str::random(4))]);    
            $exists=   Invitation::where('company_id',$company->id)->where('email',$postdata['email'])->
                        whereNull('accepted_at')->exists();  
                if($exists){
                     return back()->withErrors(['email' => 'A pending invitation already exists for this email.']);
                }

                Invitation::create([
                'company_id' => $company->id,
                'invited_by' => $user->id,
                'email'      => $postdata['email'],
                'role'       => User::ROLE_ADMIN,
                'token'      => Str::random(32),
            ]);

            return back()->with('success', "Invitation sent to {$postdata['email']} for company \"{$company->name}\".");
            }
             $data = $request->validate([
            'email'      => ['required', 'email'],
            'role'       => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_MEMBER])],
            'company_id' => ['required', 'exists:companies,id'],
            ]);  
            if ((int) $data['company_id'] !== $user->company_id) {
            abort(403, 'You can only invite users to your own company.');
             }

             $exists = Invitation::where('company_id', $data['company_id'])
            ->where('email', $data['email'])
            ->whereNull('accepted_at')
            ->exists();

        if ($exists) {
            return back()->withErrors(['email' => 'A pending invitation already exists for this email.']);
        }

        Invitation::create([
            'company_id' => $data['company_id'],
            'invited_by' => $user->id,
            'email'      => $data['email'],
            'role'       => $data['role'],
            'token'      => Str::random(32),
        ]);

        return back()->with('success', "Invitation sent to {$data['email']}.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Invitation $invitation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invitation $invitation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invitation $invitation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invitation $invitation)
    {
        //
    }


    function showAccept(string $token){
        $invitation= Invitation::where('token',$token)->whereNull('accepted_at')->first();

        return view('invitations.accept',compact('invitation'));
    }

    function accept(Request $request,string $token){
          $invitation = Invitation::where('token', $token)
            ->whereNull('accepted_at')
            ->firstOrFail();

         $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $userExists = User::where('email', $invitation->email)->exists();
        if ($userExists) {
            return redirect()->route('login')
                ->with('info', 'An account with this email already exists. Please log in.');
        }

        $newUser = User::create([
            'name'       => $data['name'],
            'email'      => $invitation->email,
            'password'   => $data['password'],
            'role'       => $invitation->role,
            'company_id' => $invitation->company_id,
        ]);

        $invitation->update(['accepted_at' => now()]);

        Auth::login($newUser);

        return redirect()->route('dashboard');
    }
}
