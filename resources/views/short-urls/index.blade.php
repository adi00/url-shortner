@extends('layouts.index')

@section('content')
<div class="panel">
    <div class="panel-header">
        <span class="panel-title">
            @if(auth()->user()->isSuperAdmin())
                All Short URLs
            @elseif(auth()->user()->isAdmin())
                Company Short URLs — {{ auth()->user()->company->name }}
            @else
                My Short URLs
            @endif
        </span>
        <div style="display:flex; gap:8px; align-items:center;">

             <form method="GET" action="{{ route('short-urls.index') }}" style="display:flex; gap:6px; align-items:center;">
                @if(auth()->user()->isSuperAdmin())
                    <select name="filter_company" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Companies</option>
                        @foreach($allCompanies ?? [] as $c)
                            <option value="{{ $c->id }}" {{ request('filter_company') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                @elseif(auth()->user()->isAdmin())
                    <select name="filter_member" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Members</option>
                        @foreach($allMembers ?? [] as $m)
                            <option value="{{ $m->id }}" {{ request('filter_member') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                        @endforeach
                    </select>
                @endif
                <select name="filter_period" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Time</option>
                    <option value="today"      {{ request('filter_period') === 'today'      ? 'selected' : '' }}>Today</option>
                    <option value="last_week"  {{ request('filter_period') === 'last_week'  ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="this_month" {{ request('filter_period') === 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ request('filter_period') === 'last_month' ? 'selected' : '' }}>Last Month</option>
                </select>
                @if(request()->hasAny(['filter_company','filter_member','filter_period']))
                    <a href="{{ route('short-urls.index') }}" class="btn btn-gray btn-sm">✕ Clear</a>
                @endif
            </form>
            @if(!auth()->user()->isSuperAdmin())
                <a href="{{ route('short-urls.create') }}" class="btn btn-orange">+ Generate</a>
            @endif
            <a href="{{ route('short-urls.export',request()->only(['filter_company','filter_member','filter_period'])) }}" class="btn btn-gray btn-sm">Download</a>
        </div>
    </div>

    <div class="panel-body" style="padding:0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Short URL</th>
                    <th>Long URL</th>
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                        <th>Created By</th>
                    @endif
                    @if(auth()->user()->isSuperAdmin())
                        <th>Company</th>
                    @endif
                    <th>Created At</th>
                    @if(!auth()->user()->isSuperAdmin())
                        <th></th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($shortUrls as $url)
                <tr>
                    <td>
                        <a href="{{ route('short-urls.redirect', $url->code) }}" target="_blank">/s/{{ $url->code }}</a>
                    </td>
                    <td class="url-cell">
                        <a href="{{ $url->original_url }}" target="_blank" title="{{ $url->original_url }}">{{ $url->original_url }}</a>
                    </td>
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                        <td>{{ $url->user->name }}</td>
                    @endif
                    @if(auth()->user()->isSuperAdmin())
                        <td>{{ $url->company->name }}</td>
                    @endif
                    <td>{{ $url->created_at->format('d M Y') }}</td>
                    @if(!auth()->user()->isSuperAdmin())
                        <td>
                            <form method="POST" action="{{ route('short-urls.destroy', $url) }}" onsubmit="return confirm('Delete this short URL?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-red btn-sm">Delete</button>
                            </form>
                        </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="color:#888; text-align:center; padding:20px;">
                        No short URLs found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="table-footer" style="padding: 8px 14px;">
            <span>Showing {{ $shortUrls->firstItem() ?? 0 }}–{{ $shortUrls->lastItem() ?? 0 }} of {{ $shortUrls->total() }} total</span>
            <div class="pagination-links">
                {{ $shortUrls->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
