@if (!config('url-export.needs_url_export_capability') || auth()->user()->hasCapabilityOnModule('url-export', $domain, $module))
    @widget(\Uccello\UrlExport\Widgets\UrlExportWidget::class, [
        'domain' => $domain,
        'module' => $module,
        'user' => auth()->user()
    ])
@endif

@section('extra-script')
    {{ Html::script(mix('js/script.js', 'vendor/uccello/url-export')) }}
@append
