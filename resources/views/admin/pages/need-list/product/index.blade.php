@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.product.index')}}">محصولات</a></li>
    <li class="active"><a href="#">نیازمندی محصولات</a></li>
@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul class="has-divider-left">
                @foreach(LayoutService::getLayoutMethods() as $layout_method)
                    <li href="{{route('admin.null')}}?layout_model=NeedList&layout_method={{$layout_method["method"]}}"
                        act="link"
                        @if($layout_method["method"] == LayoutService::getRecord("NeedList")->getMethod()) class="active" @endif>
                        <i class="fa {{$layout_method["icon"]}}"></i>
                    </li>
                @endforeach
            </ul>
            <ul>
                @foreach(SortService::getSortableFields('NeedList') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=NeedList&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
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
                @include('admin.pages.need-list.product.layout.list')
            </div>
            <div class="fab-container">
                @include('admin.templates.buttons.fab-buttons', ['buttons' => ['download']])
            </div>
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "NeedList",
            "lastPage" => $need_lists->lastPage(),
            "total" => $need_lists->total(),
            "count" => $need_lists->perPage(),
            "parentId" => $scope ?? null
        ])
    </div>
@endsection
