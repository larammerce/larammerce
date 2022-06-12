@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.rate.index')}}">امتیازات کاربران</a></li>
    <li class="active"><a href="{{route('admin.rate.index')}}">لیست امتیازات</a></li>
@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul>
                @foreach(SortService::getSortableFields('Rate') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=Rate&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
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
                @include('admin.pages.rate.layout.list')
            </div>
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "Rate",
            "lastPage" => $rates->lastPage(),
            "total" => $rates->total(),
            "count" => $rates->perPage(),
            "parentId" => $scope ?? null
        ])
    </div>
@endsection
