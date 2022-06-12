@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.customer-address.index')}}">آدرس خریداران</a></li>
    <li class="active"><a href="{{route('admin.customer-address.index')}}">لیست آدرس خریداران</a></li>
@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul class="has-divider-left">
                <li class="btn btn-default" href="{{route('admin.customer-user.index')}}" act="link">
                    <i class="fa fa-shopping-cart"></i>خریداران
                </li>
                <li class="btn btn-default" href="{{route('admin.state.index')}}" act="link">
                    <i class="fa fa-globe"></i>استان ها
                </li>
                <li class="btn btn-default" href="{{route('admin.city.index')}}" act="link">
                    <i class="fa fa-map-o"></i>شهر ها
                </li>
                <li class="btn btn-default" href="{{route('admin.district.index')}}" act="link">
                    <i class="fa fa-map-marker"></i>منطقه ها
                </li>
            </ul>
            <ul class="has-divider-left">
                @foreach(LayoutService::getLayoutMethods() as $layout_method)
                    <li href="{{route('admin.null')}}?layout_model=CustomerAddress&layout_method={{$layout_method["method"]}}"
                        act="link"
                        @if($layout_method["method"] == LayoutService::getRecord("CustomerAddress")->getMethod()) class="active" @endif>
                        <i class="fa {{$layout_method["icon"]}}"></i>
                    </li>
                @endforeach
            </ul>
            <ul>
                @foreach(SortService::getSortableFields('CustomerAddress') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=CustomerAddress&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
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
                @include('admin.pages.customer-address.layout.'.LayoutService::getRecord("CustomerAddress")->getMethod())
            </div>
            @if(isset($customer_user))
                <div class="fab-container">
                    <div class="fab green">
                        <button act="link"
                                href="{{route('admin.customer-address.create')}}?customer_user_id={{$customer_user->id}}">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            @endif
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "CustomerAddress",
            "lastPage" => $customer_addresses->lastPage(),
            "total" => $customer_addresses->total(),
            "count" => $customer_addresses->perPage(),
            "parentId" => (isset($customer_user) ? $customer_user->id : $scope ?? null)
        ])
    </div>
@endsection
