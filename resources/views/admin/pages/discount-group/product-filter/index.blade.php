@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.discount-group.index')}}">پلن های تخفیف</a></li>
    <li><a href="{{route('admin.discount-group.edit', $discount_group)}}">{{$discount_group->title}}</a></li>
    <li><a href="{{route('admin.discount-group.product-filter.index', $discount_group)}}">لیست فیلترهای محصول</a></li>
@endsection

@section('main_content')
    <div class="inner-container">
        <div class="inner-container has-toolbar has-pagination">
            <div class="view-port">
                @include('admin.pages.discount-group.product-filter.layout.list')
            </div>
            <div class="fab-container">
                <div class="fab green">
                    <button act="link" href="{{route('admin.discount-group.product-filter.create', $discount_group)}}">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "DiscountGroup",
            "lastPage" => $product_filters->lastPage(),
            "total" => $product_filters->total(),
            "count" => $product_filters->perPage(),
            "parentId" => $discount_group->id
        ])
    </div>
@endsection
