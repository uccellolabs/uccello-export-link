@if (!config('url-export.needs_url_export_capability') || auth()->user()->hasCapabilityOnModule('url-export', $domain, $module))
<div class="row">
    <div id="export-urls-list" class="col s12" style="padding: 20px">
        @foreach ($userLinks as $link)
            @include('url-export::partials.link')
        @endforeach
    </div>
</div>
@endif
