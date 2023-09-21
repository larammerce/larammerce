@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.customer-user.index')}}">خریداران</a></li>
    <li class="active"><a href="{{route('admin.customer-user.index')}}">لیست خریداران</a></li>

@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul class="has-divider-left">
                <li class="btn btn-default" href="{{route('admin.user.index')}}" act="link">
                    <i class="fa fa-users"></i>کاربران
                </li>
                <li class="btn btn-default" href="{{route('admin.system-user.index')}}" act="link">
                    <i class="fa fa-user"></i>کاربران سیستمی
                </li>
                <li class="btn btn-default" href="{{route('admin.customer-address.index')}}" act="link">
                    <i class="fa fa-location-arrow"></i>آدرس خریداران
                </li>
                <li class="btn btn-default" href="{{route('admin.customer-user-legal-info.index')}}" act="link">
                    <i class="fa fa-legal"></i>اطلاعات کاربران حقوقی
                </li>
            </ul>
            <ul class="has-divider-left">
                @foreach(LayoutService::getLayoutMethods() as $layout_method)
                    <li href="{{route('admin.null')}}?layout_model=CustomerUser&layout_method={{$layout_method["method"]}}"
                        act="link"
                        @if($layout_method["method"] == LayoutService::getRecord("CustomerUser")->getMethod()) class="active" @endif>
                        <i class="fa {{$layout_method["icon"]}}"></i>
                    </li>
                @endforeach
            </ul>
            <ul>
                @foreach(SortService::getSortableFields('CustomerUser') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=CustomerUser&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
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
                @include('admin.pages.customer-user.layout.'.LayoutService::getRecord("CustomerUser")->getMethod())
            </div>
            <div class="fab-container">
                <div class="fab green">
                    <button act="link" href="{{route('admin.user.create')}}">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "CustomerUser",
            "lastPage" => $customer_users->lastPage(),
            "total" => $customer_users->total(),
            "count" => $customer_users->perPage(),
            "parentId" => $scope ?? null
        ])
    </div>
@endsection

@section('side_content')
    <div>
        <a href="{{route('admin.customer-user.index')}}?follow=1">1)دارای فاکتور با مبلغ کمتر از 10000 تومان</a>
    </div>
    <br><br>
    <div>
        <a href="{{route('admin.customer-user.index')}}?follow=2">2)دارای فاکتور با مبلغ کمتر از 2000000 تومان</a>
    </div>
    <br><br>
    <div>
        <a href="{{route('admin.customer-user.index')}}?follow=3">3)دارای فاکتور پرداخت شده</a>
    </div>
@endsection