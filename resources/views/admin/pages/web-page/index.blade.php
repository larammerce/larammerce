@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.web-page.index')}}">صفحات وب</a></li>
    <li class="active"><a href="{{route('admin.web-page.index')}}">لیست صفحات وب</a></li>

@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul class="has-divider-left">
                <li class="btn btn-default" href="{{route('admin.web-form.index')}}" act="link">
                    <i class="fa fa-wpforms"></i>فرم ها
                </li>
                <li class="btn btn-default" href="{{route('admin.gallery.index')}}" act="link">
                    <i class="fa fa-photo"></i>گالری
                </li>
            </ul>
            <ul class="has-divider-left">
                @foreach(LayoutService::getLayoutMethods() as $layout_method)
                    <li href="{{route('admin.null')}}?layout_model=WebPage&layout_method={{$layout_method["method"]}}"
                        act="link"
                        @if($layout_method["method"] == LayoutService::getRecord("WebPage")->getMethod()) class="active" @endif>
                        <i class="fa {{$layout_method["icon"]}}"></i>
                    </li>
                @endforeach
            </ul>
            <ul>
                @foreach(SortService::getSortableFields('WebPage') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=WebPage&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
                        act="link">
                        @if($sortable_field->is_active)
                            <i class="fa {{$sortable_field->method == SortMethod::ASCENDING ?
                        "fa-long-arrow-up" : "fa-long-arrow-down"}}"></i>
                        @endif
                        {{$sortable_field->title}}
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="inner-container has-toolbar has-pagination">
            <div class="view-port">
                @include('admin.pages.web-page.layout.'.LayoutService::getRecord("WebPage")->getMethod())
            </div>
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "WebPage",
            "lastPage" => $web_pages->lastPage(),
            "total" => $web_pages->total(),
            "count" => $web_pages->perPage(),
            "parentId" => $scope ?? null
        ])
    </div>
@endsection
