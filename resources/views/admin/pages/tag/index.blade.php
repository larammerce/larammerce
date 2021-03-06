@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.tag.index')}}">تگ ها</a></li>
    <li class="active"><a href="{{route('admin.tag.index')}}">لیست تگ ها</a></li>

@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul class="has-divider-left">
                @foreach(LayoutService::getLayoutMethods() as $layout_method)
                    <li href="{{route('admin.null')}}?layout_model=Tag&layout_method={{$layout_method["method"]}}"
                        act="link"
                        @if($layout_method["method"] == LayoutService::getRecord("Tag")->getMethod()) class="active" @endif>
                        <i class="fa {{$layout_method["icon"]}}"></i>
                    </li>
                @endforeach
            </ul>
            <ul>
                @foreach(SortService::getSortableFields('Tag') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=Tag&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
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
                @include('admin.pages.tag.layout.'.LayoutService::getRecord("Tag")->getMethod())
            </div>
            <div class="fab-container">
                <div class="fab green">
                    <button act="link" href="{{route('admin.tag.create')}}">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "Tag",
            "lastPage" => $tags->lastPage(),
            "total" => $tags->total(),
            "count" => $tags->perPage(),
            "parentId" => $scope ?? null
        ])
    </div>
@endsection
