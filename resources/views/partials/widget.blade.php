@if (!config('export-link.needs_export_with_link_capability') || auth()->user()->hasCapabilityOnModule('export-link', $domain, $module))
    @widget(\Uccello\ExportLink\Widgets\ExportLinkWidget::class, [
        'domain' => $domain,
        'module' => $module,
        'user' => auth()->user()
    ])
@endif

@section('extra-script')
    {{ Html::script(mix('js/script.js', 'vendor/uccello/export-link')) }}
@append
