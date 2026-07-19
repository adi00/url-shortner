@extends('layouts.index')

@section('content')
<div class="panel" style="max-width:500px;">
    <div class="panel-header">
        <span class="panel-title">
            @if(auth()->user()->isSuperAdmin())
                Invite New Client
            @else
                Invite New Team Member
            @endif
        </span>
    </div>
    <div class="panel-body">
        <form method="POST" action="{{ route('invitations.store') }}">
            @csrf

            @if(auth()->user()->isSuperAdmin())
               
                <div class="form-group">
                    <label for="company_name">Name:</label>
                    <input type="text" id="company_name" name="company_name"
                           class="form-control"
                           placeholder="Client name..."
                           value="{{ old('company_name') }}">
                    @error('company_name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email"
                           class="form-control"
                           placeholder="e.g. admin@example.com"
                           value="{{ old('email') }}"
                           required>
                    @error('email')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                
                <input type="hidden" name="role" value="admin">
                <input type="hidden" name="company_id" value="new">

            @else
              
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="invitee_name"
                           class="form-control"
                           placeholder="Full name (optional)"
                           value="{{ old('invitee_name') }}">
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email"
                           class="form-control"
                           placeholder="e.g. member@example.com"
                           value="{{ old('email') }}"
                           required>
                    @error('email')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="role">Role:</label>
                    <select id="role" name="role" class="form-control" required>
                        <option value="">— Select —</option>
                        <option value="admin"  @selected(old('role') === 'admin')>Admin</option>
                        <option value="member" @selected(old('role') === 'member')>Member</option>
                    </select>
                    @error('role')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <input type="hidden" name="company_id" value="{{ auth()->user()->company_id }}">

            @endif

            <div style="display:flex; gap:8px; margin-top:4px;">
                <button type="submit" class="btn btn-orange">Send Invitation</button>
                <a href="{{ route('dashboard') }}" class="btn btn-gray">Cancel</a>
            </div>
        </form>
    </div>
</div>

@if(!auth()->user()->isSuperAdmin())

<div class="panel">
    <div class="panel-header">
        <span class="panel-title">Team Members</span>
        <span style="font-size:11px; color:#888;">{{ auth()->user()->company->name }}</span>
    </div>
    <div class="panel-body" style="padding:0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Total Generated URLs</th>
                    <th>Total URL Hits</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teamMembers as $member)
                <tr>
                    <td>{{ $member->name }}</td>
                    <td>{{ $member->email }}</td>
                    <td><span class="badge badge-{{ $member->role }}">{{ ucfirst($member->role) }}</span></td>
                    <td class="stat-num">{{ $member->short_urls_count }}</td>
                    <td class="stat-num">{{ $member->short_urls_count }}</td>
                </tr>
                @empty
                <tr><td colspan="5" style="color:#888; text-align:center; padding:16px;">No team members yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="table-footer" style="padding: 8px 14px;">
            <span>Showing {{ $teamMembers->count() }} of {{ $teamMembers->total() }} total</span>
            @if($teamMembers->total() > $teamMembers->perPage())
                <div class="pagination-links">{{ $teamMembers->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endif

@endsection
