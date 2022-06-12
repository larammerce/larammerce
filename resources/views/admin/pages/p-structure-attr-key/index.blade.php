@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.p-structure-attr-key.index')}}">ويژگی های محصولات</a></li>
    <li class="active"><a href="{{route('admin.p-structure-attr-key.index')}}">لیست ويژگی های محصولات</a></li>

@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul class="has-divider-left">
                <li class="btn btn-default"
                    href="{{route('admin.p-structure.index')}}" act="link">
                    <i class="fa fa-wpforms"></i>
                    قالب ها
                </li>
                <li class="btn btn-default"
                    href="{{route('admin.p-structure-attr-value.index')}}" act="link">
                    <i class="fa fa-navicon"></i>
                    مقادیر
                </li>
            </ul>
            <ul class="has-divider-left">
                @foreach(LayoutService::getLayoutMethods() as $layout_method)
                    <li href="{{route('admin.null')}}?layout_model=ProductStructureAttributeKey&layout_method={{$layout_method["method"]}}"
                        act="link"
                        @if($layout_method["method"] == LayoutService::getRecord("ProductStructureAttributeKey")->getMethod()) class="active" @endif>
                        <i class="fa {{$layout_method["icon"]}}"></i>
                    </li>
                @endforeach
            </ul>
            <ul>
                @foreach(SortService::getSortableFields('ProductStructureAttributeKey') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=ProductStructureAttributeKey&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
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
                @include('admin.pages.p-structure-attr-key.layout.'.LayoutService::getRecord("ProductStructureAttributeKey")->getMethod())
            </div>
            <div class="fab-container">
                <div class="fab green">
                    <button act="link" href="{{route('admin.p-structure-attr-key.create')}}">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "ProductStructureAttributeKey",
            "lastPage" => $attribute_keys->lastPage(),
            "total" => $attribute_keys->total(),
            "count" => $attribute_keys->perPage(),
            "parentId" => $scope ?? null
        ])
    </div>
@endsection
