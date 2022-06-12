@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.gallery.index')}}">گالری ها</a></li>
    <li class="active"><a href="{{route('admin.gallery.index')}}">لیست گالری ها</a></li>

@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul class="has-divider-left">
                @foreach(LayoutService::getLayoutMethods() as $layout_method)
                    <li href="{{route('admin.null')}}?layout_model=Gallery&layout_method={{$layout_method["method"]}}"
                        act="link"
                        @if($layout_method["method"] == LayoutService::getRecord("Gallery")->getMethod()) class="active" @endif>
                        <i class="fa {{$layout_method["icon"]}}"></i>
                    </li>
                @endforeach
            </ul>
            <ul>
                @foreach(SortService::getSortableFields('Gallery') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=Gallery&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
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
                @include('admin.pages.gallery.layout.'.LayoutService::getRecord("Gallery")->getMethod())
            </div>
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "Gallery",
            "lastPage" => $galleries->lastPage(),
            "total" => $galleries->total(),
            "count" => $galleries->perPage(),
            "parentId" => $scope ?? null
        ])
    </div>
@endsection
