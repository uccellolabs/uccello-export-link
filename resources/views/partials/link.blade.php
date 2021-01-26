<div class="row blue lighten-5 export-url" style="padding: 15px 10px; border-radius: 7px">
    <div class="col s12" style="margin-bottom: 10px">
        @if ($link->columns)<span class="badge blue lighten-1 white-text left" data-tooltip="- {{ $link->columnsLabels->join('<br> - ') }}">@lang('url-export::url-export.option.columns')</span>@endif
        @if ($link->order)<span class="badge lime darken-1 white-text left">@lang('url-export::url-export.option.order')</span>@endif
        @if ($link->withDescendants)<span class="badge red white-text left">@lang('url-export::url-export.option.with_descendants')</span>@endif
        @if ($link->withId)<span class="badge grey white-text left">@lang('url-export::url-export.option.with_id')</span>@endif
        @if ($link->withTimestamps)<span class="badge grey white-text left">@lang('url-export::url-export.option.with_timestamps')</span>@endif
        @if ($link->conditions)<span class="badge orange white-text left" data-tooltip="{{ $link->userFriendlyConditions->join('<br>') }}">@lang('url-export::url-export.option.conditions')</span>@endif
        <span class="badge green white-text left">.{{ $link->extension ?? 'csv' }}</span>
    </div>
    <div class="col s8 m9">
        @php($linkUrl = ucroute('url-export.export', $link->domain, $link->module, ['uuid' => $link->uuid]))
        {{-- <a href="{{ $linkUrl }}" class="primary-text left" style="margin-top: 7px">{{ $linkUrl }}</a> --}}
        <input id="{{ $link->uuid }}" type="text" class="export-url-value" value="{{ $linkUrl }}">
    </div>
    <div class="col s4 m3">
        <a href="{{ ucroute('url-export.delete', $link->domain, $link->module, ['uuid' => $link->uuid]) }}" class="btn-flat red-text right delete-url" data-tooltip="Delete URL" data-position="top"><i class="material-icons">delete</i></a>
        <a href="#" class="btn-flat primary-text right copy-url" data-tooltip="Copy URL" data-position="top"><i class="material-icons">content_copy</i></a>
    </div>
</div>
