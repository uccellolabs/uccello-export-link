@if (!config('url-export.needs_url_export_capability') || auth()->user()->hasCapabilityOnModule('url-export', $domain, $module))
    <a href="{{ ucroute('url-export.generate', $domain, $module) }}" class="btn-flat primary-text generate-export-url">@lang('url-export::url-export.button.generate_url')</a>
@endif
