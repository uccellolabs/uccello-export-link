@if (!config('export-link.needs_export_with_link_capability') || auth()->user()->hasCapabilityOnModule('export-link', $domain, $module))
<div class="row">
    <div id="export-links-list" class="col s12" style="padding: 20px">
        @foreach ($userLinks as $link)
            @include('export-link::partials.link')
        @endforeach
    </div>
</div>
@endif
