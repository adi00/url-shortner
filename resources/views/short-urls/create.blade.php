@extends('layouts.index')

@section('content')
<div class="panel" style="max-width:600px;">
    <div class="panel-header">
        <span class="panel-title">Generate Short URL</span>
    </div>
    <div class="panel-body">
        <form method="POST" action="{{ route('short-urls.store') }}">
            @csrf
            <div class="form-group">
                <label for="original_url">URL</label>
                <input type="url" id="original_url" name="original_url"
                       class="form-control"
                       placeholder="e.g. https://docs.example.com/features/path-to-long-url"
                       value="{{ old('original_url') }}"
                       required>
                @error('original_url')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div style="display:flex; gap:8px;">
                <button type="submit" class="btn btn-orange">Generate</button>
                <a href="{{ route('dashboard') }}" class="btn btn-gray">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
