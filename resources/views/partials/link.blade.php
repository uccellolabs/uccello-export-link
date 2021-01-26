<div class="row blue lighten-5 export-link" style="padding: 15px 10px; border-radius: 7px">
    <div class="col s12" style="margin-bottom: 10px">
        @if ($link->columns)<span class="badge blue lighten-1 white-text left" data-tooltip="- {{ $link->columnsLabels->join('<br> - ') }}">@lang('export-link::export-link.option.columns')</span>@endif
        @if ($link->order)<span class="badge lime darken-1 white-text left">@lang('export-link::export-link.option.order')</span>@endif
        @if ($link->withDescendants)<span class="badge red white-text left">@lang('export-link::export-link.option.with_descendants')</span>@endif
        @if ($link->withId)<span class="badge grey white-text left">@lang('export-link::export-link.option.with_id')</span>@endif
        @if ($link->withTimestamps)<span class="badge grey white-text left">@lang('export-link::export-link.option.with_timestamps')</span>@endif
        @if ($link->conditions)<span class="badge orange white-text left" data-tooltip="{{ $link->userFriendlyConditions->join('<br>') }}">@lang('export-link::export-link.option.conditions')</span>@endif
        <span class="badge green white-text left">.{{ $link->extension ?? 'csv' }}</span>
    </div>
    <div class="col s8 m9">
        @php($linkUrl = ucroute('export-link.export', $link->domain, $link->module, ['uuid' => $link->uuid]))
        <input id="{{ $link->uuid }}" type="text" class="export-link-value" value="{{ $linkUrl }}">
    </div>
    <div class="col s4 m3">
        <a href="{{ ucroute('export-link.delete', $link->domain, $link->module, ['uuid' => $link->uuid]) }}" class="btn-flat red-text right delete-url" data-tooltip="@lang('export-link::export-link.button.delete_link')" data-position="top"><i class="material-icons">delete</i></a>
        <a href="#" class="btn-flat primary-text right copy-url" data-tooltip="@lang('export-link::export-link.button.copy_link')" data-position="top"><i class="material-icons">content_copy</i></a>
    </div>
</div>
