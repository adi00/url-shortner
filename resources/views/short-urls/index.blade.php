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
            @if(!auth()->user()->isSuperAdmin())
                <a href="{{ route('short-urls.create') }}" class="btn btn-orange">+ Generate</a>
            @endif
            <a href="{{ route('short-urls.export') }}" class="btn btn-gray btn-sm">Download</a>
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
