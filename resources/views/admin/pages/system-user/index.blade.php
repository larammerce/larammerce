@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.system-user.index')}}">کاربران سیستمی</a></li>
    <li class="active"><a href="{{route('admin.system-user.index')}}">لیست کاربران سیستمی</a></li>

@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul class="has-divider-left">
                <li class="btn btn-default"
                    href="{{route('admin.user.index')}}" act="link">
                    <i class="fa fa-users"></i>
                    کاربران
                </li>
                <li class="btn btn-default"
                    href="{{route('admin.customer-user.index')}}" act="link">
                    <i class="fa fa-shopping-cart"></i>
                    خریداران
                </li>
            </ul>
            <ul class="has-divider-left">
                @foreach(LayoutService::getLayoutMethods() as $layout_method)
                    <li href="{{route('admin.null')}}?layout_model=SystemUser&layout_method={{$layout_method["method"]}}"
                        act="link"
                        @if($layout_method["method"] == LayoutService::getRecord("SystemUser")->getMethod()) class="active" @endif>
                        <i class="fa {{$layout_method["icon"]}}"></i>
                    </li>
                @endforeach
            </ul>
            <ul>
                @foreach(SortService::getSortableFields('SystemUser') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=SystemUser&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
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

                @include('admin.pages.system-user.layout.'.LayoutService::getRecord("SystemUser")->getMethod())
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
            "modelName" => "SystemUser",
            "lastPage" => $system_users->lastPage(),
            "total" => $system_users->total(),
            "count" => $system_users->perPage(),
            "parentId" => $scope ?? null
        ])
    </div>
@endsection
