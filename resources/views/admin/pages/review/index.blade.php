@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.color.index')}}">مطالب نیازمند بررسی</a></li>
    <li class="active"><a href="{{route('admin.color.index')}}">لیست</a></li>

@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul class="has-divider-left">
                @foreach(LayoutService::getLayoutMethods() as $layout_method)
                    <li href="{{route('admin.null')}}?layout_model=Review&layout_method={{$layout_method["method"]}}"
                        act="link"
                        @if($layout_method["method"] == LayoutService::getRecord("Review")->getMethod()) class="active" @endif>
                        <i class="fa {{$layout_method["icon"]}}"></i>
                    </li>
                @endforeach
            </ul>
            <ul>
                @foreach(SortService::getSortableFields('Review') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=Review&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
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
                @include('admin.pages.review.layout.'.LayoutService::getRecord("Review")->getMethod())
            </div>
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "Review",
            "lastPage" => $reviews->lastPage(),
            "total" => $reviews->total(),
            "count" => $reviews->perPage(),
            "parentId" => $scope ?? null
        ])
    </div>
@endsection
