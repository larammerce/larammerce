@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.p-structure.index')}}">قالب محصولات</a></li>
    <li class="active"><a href="{{route('admin.p-structure.index')}}">لیست قالب محصولات</a></li>

@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul class="has-divider-left">
                <li class="btn btn-default" href="{{route('admin.p-structure-attr-key.index')}}" act="link">
                    <i class="fa fa-key"></i>کلید ها
                </li>
                <li class="btn btn-default" href="{{route('admin.p-structure-attr-value.index')}}" act="link">
                    <i class="fa fa-navicon"></i>مقادیر
                </li>
                <li class="btn btn-default" href="{{route('admin.product.index')}}" act="link">
                    <i class="fa fa-cubes"></i>محصولات
                </li>
            </ul>
            <ul class="has-divider-left">
                @foreach(LayoutService::getLayoutMethods() as $layout_method)
                    <li href="{{route('admin.null')}}?layout_model=ProductStructure&layout_method={{$layout_method["method"]}}"
                        act="link"
                        @if($layout_method["method"] == LayoutService::getRecord("ProductStructure")->getMethod()) class="active" @endif>
                        <i class="fa {{$layout_method["icon"]}}"></i>
                    </li>
                @endforeach
            </ul>
            <ul>
                @foreach(SortService::getSortableFields('ProductStructure') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=ProductStructure&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
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
                @include('admin.pages.p-structure.layout.'.LayoutService::getRecord("ProductStructure")->getMethod())
            </div>
            <div class="fab-container">
                <div class="fab green">
                    <button act="link" href="{{route('admin.p-structure.create')}}">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "ProductStructure",
            "lastPage" => $p_structures->lastPage(),
            "total" => $p_structures->total(),
            "count" => $p_structures->perPage(),
            "parentId" => $scope ?? null
        ])
    </div>
@endsection
