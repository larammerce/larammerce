@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.customer-user.index')}}">خریداران</a></li>
    <li class="active"><a href="{{route('admin.customer-user-legal-info.index')}}">لیست اطلاعات حقوقی خریداران</a></li>

@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul class="has-divider-left">
                <li class="btn btn-default" href="{{route('admin.user.index')}}" act="link">
                    <i class="fa fa-users"></i>کاربران
                </li>
                <li class="btn btn-default" href="{{route('admin.customer-user.index')}}" act="link">
                    <i class="fa fa-shopping-cart"></i>خریداران
                </li>
            </ul>
            <ul class="has-divider-left">
                @foreach(LayoutService::getLayoutMethods() as $layout_method)
                    <li href="{{route('admin.null')}}?layout_model=CustomerUserLegalInfo&layout_method={{$layout_method["method"]}}"
                        act="link"
                        @if($layout_method["method"] == LayoutService::getRecord("CustomerUserLegalInfo")->getMethod()) class="active" @endif>
                        <i class="fa {{$layout_method["icon"]}}"></i>
                    </li>
                @endforeach
            </ul>
            <ul>
                @foreach(SortService::getSortableFields('CustomerUserLegalInfo') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=CustomerUserLegalInfo&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
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
                @include('admin.pages.customer-user-legal-info.layout.'.LayoutService::getRecord("CustomerUserLegalInfo")->getMethod())
            </div>
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "CustomerUserLegalInfo",
            "lastPage" => $customer_users_legal_info->lastPage(),
            "total" => $customer_users_legal_info->total(),
            "count" => $customer_users_legal_info->perPage(),
            "parentId" => $scope ?? null
        ])
    </div>
@endsection
