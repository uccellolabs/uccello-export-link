@if (!config('export-link.needs_export_with_link_capability') || auth()->user()->hasCapabilityOnModule('export-link', $domain, $module))
    <a href="{{ ucroute('export-link.generate', $domain, $module) }}" class="btn-flat primary-text generate-export-link">@lang('export-link::export-link.button.generate_link')</a>
@endif
