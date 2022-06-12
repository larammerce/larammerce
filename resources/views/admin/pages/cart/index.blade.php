@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.cart.index')}}">سبدهای خرید</a></li>
    <li class="active"><a href="{{route('admin.cart.index')}}">لیست سبدها</a></li>

@endsection

@section('main_content')
    <div class="inner-container">
        <div class="inner-container has-toolbar has-pagination">
            <div class="view-port">
                @include('admin.pages.cart.layout.list')
            </div>
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "CartRow",
            "lastPage" => $cart_owners->lastPage(),
            "total" => $cart_owners->total(),
            "count" => $cart_owners->perPage(),
            "parentId" => $scope ?? null
        ])
    </div>
@endsection
