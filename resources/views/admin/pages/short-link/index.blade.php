@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.short-link.index')}}">لینک های کوتاه</a></li>
    <li class="active"><a href="{{route('admin.short-link.index')}}">لیست لینک ها</a></li>

@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul>
                @foreach(SortService::getSortableFields('ShortLink') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=ShortLink&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
                        act="link">
                        @if($sortable_field->is_active)
                            <i class="fa {{$sortable_field->method == SortMethod::ASCENDING ? "fa-long-arrow-up" : "fa-long-arrow-down"}}"></i>
                        @endif
                        {{$sortable_field->title}}
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="inner-container has-toolbar has-pagination">
            <div class="view-port">
                @include('admin.pages.short-link.layout.list')
            </div>
            <div class="fab-container">
                <div class="fab green">
                    <button act="link" href="{{route('admin.short-link.create')}}">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        @include('admin.templates.pagination', [
        "modelName" => "ShortLink",
        "lastPage" => $short_links->lastPage(),
        "total" => $short_links->total(),
        "count" => $short_links->perPage(),
        "parentId" => $scope ?? null
        ])
    </div>
@endsection
