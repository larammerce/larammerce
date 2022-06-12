@extends('admin.layout')

@section('extra_style')
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.13.0/css/all.css">
@endsection

@section('bread_crumb')
    <li class="active"><a href="{{route('admin.badge.index')}}">نشان ها</a></li>

@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul>
                @foreach(SortService::getSortableFields('Badge') as $sortableField)
                    <li class="btn btn-default {{$sortableField->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=Badge&sort_field={{$sortableField->field}}&sort_method={{$sortableField->method}}"
                        act="link">
                        @if($sortableField->is_active)
                            <i class="fa {{$sortableField->method == SortMethod::ASCENDING ? "fa-long-arrow-up" : "fa-long-arrow-down"}}"></i>
                        @endif
                        {{$sortableField->title}}
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="inner-container has-toolbar has-pagination">
            <div class="view-port">
                @include('admin.pages.badge.layout.list')
            </div>
            <div class="fab-container">
                <div class="fab green">
                    <button act="link" href="{{route('admin.badge.create')}}">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        @include('admin.templates.pagination', [
        "modelName" => "Badge",
        "lastPage" => $badges->lastPage(),
        "total" => $badges->total(),
        "count" => $badges->perPage()
        ])
    </div>
@endsection
