@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.product-special-price.index')}}">قیمت ویژه محصولات</a></li>
    <li class="active"><a href="{{route('admin.product-special-price.index')}}">لیست قیمت های ویژه</a></li>

@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul class="has-divider-left">
                <li class="btn btn-default" href="{{route('admin.product.edit', $product)}}" act="link">
                    <i class="fa fa-cube"></i>بازگشت به {{ $product->title }}
                </li>
            </ul>
            <ul class="has-divider-left">
                @foreach(LayoutService::getLayoutMethods() as $layout_method)
                    <li href="{{route('admin.null')}}?layout_model=ProductSpecialPrice&layout_method={{$layout_method["method"]}}"
                        act="link"
                        @if($layout_method["method"] == LayoutService::getRecord("ProductSpecialPrice")->getMethod()) class="active" @endif>
                        <i class="fa {{$layout_method["icon"]}}"></i>
                    </li>
                @endforeach
            </ul>
            <ul>
                @foreach(SortService::getSortableFields('ProductSpecialPrice') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=ProductSpecialPrice&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
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
                @include('admin.pages.product-special-price.layout.'.LayoutService::getRecord("ProductSpecialPrice")->getMethod())
            </div>
            <div class="fab-container">
                <div class="fab green">
                    <button act="link"
                            href="{{route('admin.product-special-price.create')}}?product_id={{$product->id}}">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "ProductSpecialPrice",
            "lastPage" => $product_special_prices->lastPage(),
            "total" => $product_special_prices->total(),
            "count" => $product_special_prices->perPage(),
            "parentId" => isset($product) ? $product->id : $scope ?? null
        ])
    </div>
@endsection
-
