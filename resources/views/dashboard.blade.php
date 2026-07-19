@extends('layouts.index')

@section('content')
@if($authUser->isSuperAdmin())
<div class="panel">
        <div class="panel-header">
            <span class="panel-title">Clients</span>
            <a href="{{ route('invitations.create') }}" class="btn btn-orange">Invite</a>
        </div>
        <div class="panel-body" style="padding:0;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Client Name</th>
                        <th>Users</th>
                        <th>Total Generated URLs</th>
                        <th>Total URL Hits</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                    <tr>
                        <td>
                            <strong>{{ $company->name }}</strong><br>
                            <span style="color:#888; font-size:11px;">{{ $company->users->first()?->email ?? '—' }}</span>
                        </td>
                        <td class="stat-num">{{ $company->users_count }}</td>
                        <td class="stat-num">{{ $company->short_urls_count }}</td>
                        <td class="stat-num">{{ $company->short_urls_count }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="color:#888; text-align:center; padding:20px;">No companies yet. Invite a client to get started.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="table-footer" style="padding: 8px 14px;">
                <span>Showing {{ $companies->count() }} of {{ $companies->total() }} total</span>
                <div class="pagination-links">
                    {!! $companies->links('pagination::simple-default') !!}
                </div>
            </div>
        </div>
    </div>


     <div class="panel">
        <div class="panel-header">
            <span class="panel-title">Generated Short URLs</span>
            <div style="display:flex; gap:8px; align-items:center;">
                <div class="filter-bar">
                    <select name="filter_company" id="filter_company" onchange="applyFilters()">
                        <option value="">This Month</option>
                        @foreach($allCompanies as $c)
                            <option value="{{ $c->id }}" {{ request('filter_company') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    <select name="filter_user" id="filter_user" onchange="applyFilters()">
                        <option value="">Last Month</option>
                        <option value="last_week" {{ request('filter_user') === 'last_week' ? 'selected' : '' }}>Last Week</option>
                        <option value="today" {{ request('filter_user') === 'today' ? 'selected' : '' }}>Today</option>
                    </select>
                </div>
                <a href="{{ route('short-urls.export') }}" class="btn btn-gray btn-sm">Download</a>
            </div>
        </div>
        <div class="panel-body" style="padding:0;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Short URL</th>
                        <th>Long URL</th>
                        <th>Users</th>
                        <th>Company</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shortUrls as $url)
                    <tr>
                        <td><a href="{{ route('short-urls.redirect', $url->code) }}" target="_blank">/s/{{ $url->code }}</a></td>
                        <td class="url-cell"><a href="{{ $url->original_url }}" target="_blank" title="{{ $url->original_url }}">{{ $url->original_url }}</a></td>
                        <td>{{ $url->user->name }}</td>
                        <td>{{ $url->company->name }}</td>
                        <td>{{ $url->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="color:#888; text-align:center; padding:20px;">No short URLs yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="table-footer" style="padding: 8px 14px;">
                <span>Showing {{ $shortUrls->firstItem() ?? 0 }}–{{ $shortUrls->lastItem() ?? 0 }} of {{ $shortUrls->total() }} total</span>
                <div class="pagination-links">
                    {!! $shortUrls->links('pagination::simple-default') !!}
                </div>
            </div>
        </div>
    </div>
@elseif($authUser->isAdmin())

    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">Generate Short URL</span>
        </div>
        <div class="panel-body">
            <form method="POST" action="{{ route('short-urls.store') }}" class="generate-row">
                @csrf
                <input type="url" name="original_url" class="form-control"
                       placeholder="e.g. https://docs.example.com/features/path-to-long-doc"
                       value="{{ old('original_url') }}" required>
                <button type="submit" class="btn btn-orange">Generate</button>
            </form>
            @error('original_url')<div class="field-error" style="margin-top:-8px; margin-bottom:8px;">{{ $message }}</div>@enderror
        </div>
    </div>

   
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">Generated Short URLs</span>
            <div style="display:flex; gap:8px; align-items:center;">
                <div class="filter-bar">
                    <select onchange="applyFilters()">
                        <option>This Month</option>
                        <option>Last Month</option>
                        <option>Last Week</option>
                        <option>Today</option>
                    </select>
                    <select onchange="applyFilters()">
                        <option>Last Path</option>
                        <option>Last Week</option>
                        <option>Today</option>
                    </select>
                </div>
                <a href="{{ route('short-urls.export') }}" class="btn btn-gray btn-sm">Download</a>
            </div>
        </div>
        <div class="panel-body" style="padding:0;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Short URL</th>
                        <th>Long URL</th>
                        <th>User</th>
                        <th>Created At</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shortUrls as $url)
                    <tr>
                        <td><a href="{{ route('short-urls.redirect', $url->code) }}" target="_blank">/s/{{ $url->code }}</a></td>
                        <td class="url-cell"><a href="{{ $url->original_url }}" target="_blank" title="{{ $url->original_url }}">{{ $url->original_url }}</a></td>
                        <td>{{ $url->user->name }}</td>
                        <td>{{ $url->created_at->format('d M Y') }}</td>
                        <td>
                            <form method="POST" action="{{ route('short-urls.destroy', $url) }}" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-red btn-sm">Del</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="color:#888; text-align:center; padding:20px;">No short URLs yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="table-footer" style="padding: 8px 14px;">
                <span>Showing {{ $shortUrls->firstItem() ?? 0 }}–{{ $shortUrls->lastItem() ?? 0 }} of {{ $shortUrls->total() }} total</span>
                @if($shortUrls->total() > $shortUrls->perPage())
                    <a href="{{ route('short-urls.index') }}" class="btn btn-blue btn-sm">View All</a>
                @endif
            </div>
        </div>
    </div>

    
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">Team Members</span>
            <a href="{{ route('invitations.create') }}" class="btn btn-orange">Invite</a>
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
                    <tr><td colspan="5" style="color:#888; text-align:center; padding:20px;">No team members yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="table-footer" style="padding: 8px 14px;">
                <span>Showing {{ $teamMembers->count() }} of {{ $teamMembers->total() }} total</span>
                @if($teamMembers->total() > $teamMembers->perPage())
                    <a href="#" class="btn btn-blue btn-sm">View All</a>
                @endif
            </div>
        </div>
    </div>
@else

    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">Generate Short URL</span>
        </div>
        <div class="panel-body">
            <form method="POST" action="{{ route('short-urls.store') }}" class="generate-row">
                @csrf
                <input type="url" name="original_url" class="form-control"
                       placeholder="e.g. https://docs.example.com/features/path-to-long-doc"
                       value="{{ old('original_url') }}" required>
                <button type="submit" class="btn btn-orange">Generate</button>
            </form>
            @error('original_url')<div class="field-error" style="margin-top:-8px; margin-bottom:8px;">{{ $message }}</div>@enderror
        </div>
    </div>

 
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title">Short URLs</span>
            <div style="display:flex; gap:8px;">
                <a href="{{ route('short-urls.export') }}" class="btn btn-gray btn-sm">Download</a>
            </div>
        </div>
        <div class="panel-body" style="padding:0;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Short URL</th>
                        <th>Long URL</th>
                        <th>Hits</th>
                        <th>Created At</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shortUrls as $url)
                    <tr>
                        <td><a href="{{ route('short-urls.redirect', $url->code) }}" target="_blank">/s/{{ $url->code }}</a></td>
                        <td class="url-cell"><a href="{{ $url->original_url }}" target="_blank" title="{{ $url->original_url }}">{{ $url->original_url }}</a></td>
                        <td>{{ $url->hits ?? 0 }}</td>
                        <td>{{ $url->created_at->format('d M Y') }}</td>
                        <td>
                            <form method="POST" action="{{ route('short-urls.destroy', $url) }}" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-red btn-sm">Del</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="color:#888; text-align:center; padding:20px;">No short URLs yet. Generate your first one above.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="table-footer" style="padding: 8px 14px;">
                <span>Showing {{ $shortUrls->firstItem() ?? 0 }}–{{ $shortUrls->lastItem() ?? 0 }} of {{ $shortUrls->total() }} total</span>
            </div>
        </div>
    </div>

@endif
@endsection