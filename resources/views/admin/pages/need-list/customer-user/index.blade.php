@extends('admin.layout')

@section('bread_crumb')
    <li><a href="#">لیست کاربران درخواستی این محصول</a></li>
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
                @include('admin.pages.need-list.customer-user.layout.list')
            </div>
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "NeedList",
            "lastPage" => $customer_users->lastPage(),
            "total" => $customer_users->total(),
            "count" => $customer_users->perPage(),
            "parentId" => "customers_p_{$product->id}"
        ])
    </div>
@endsection
