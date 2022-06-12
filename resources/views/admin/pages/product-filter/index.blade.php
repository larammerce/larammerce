@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.product-filter.index')}}">فیلتر سفارشی محصولات</a></li>
    <li class="active"><a href="{{route('admin.product-filter.index')}}">لیست فیلتر ها</a></li>

@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul>
                @foreach(SortService::getSortableFields('ProductQuery') as $sortable_filed)
                    <li class="btn btn-default {{$sortable_filed->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=ProductQuery&sort_field={{$sortable_filed->field}}&sort_method={{$sortable_filed->method}}"
                        act="link">
                        @if($sortable_filed->is_active)
                            <i class="fa {{$sortable_filed->method == SortMethod::ASCENDING ? "fa-long-arrow-up" : "fa-long-arrow-down"}}"></i>
                        @endif
                        {{$sortable_filed->title}}
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="inner-container has-toolbar has-pagination">
            <div class="view-port">
                @include('admin.pages.product-filter.layout.list')
            </div>
            <div class="fab-container">
                <div class="fab green">
                    <button act="link" href="{{route('admin.product-filter.create')}}">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        @include('admin.templates.pagination', [
        "modelName" => "ProductFilter",
        "lastPage" => $product_filters->lastPage(),
        "total" => $product_filters->total(),
        "count" => $product_filters->perPage(),
        "parentId" => $scope ?? null
        ])
    </div>
@endsection
