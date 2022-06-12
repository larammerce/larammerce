@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.web-form-message.index')}}">پیام ها</a></li>
    <li class="active"><a href="{{route('admin.web-form-message.index')}}">لیست پیام ها</a></li>

@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul class="has-divider-left">
                @foreach(LayoutService::getLayoutMethods() as $layout_method)
                    <li href="{{route('admin.null')}}?layout_model=WebForm&layout_method={{$layout_method["method"]}}"
                        act="link"
                        @if($layout_method["method"] == LayoutService::getRecord("WebFormMessage")->getMethod()) class="active" @endif>
                        <i class="fa {{$layout_method["icon"]}}"></i>
                    </li>
                @endforeach
            </ul>
            <ul>
                @foreach(SortService::getSortableFields('WebFormMessage') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=WebFormMessage&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
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
                @include('admin.pages.web-form-message.layout.'.LayoutService::getRecord("WebFormMessage")->getMethod())
            </div>
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "WebFormMessage",
            "lastPage" => $web_form_messages->lastPage(),
            "total" => $web_form_messages->total(),
            "count" => $web_form_messages->perPage(),
            "parentId" => $scope ?? null
        ])
    </div>
@endsection
