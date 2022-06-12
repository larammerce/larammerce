@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.user.index')}}">کاربران</a></li>
    <li class="active"><a href="{{route('admin.user.index')}}">لیست کاربران</a></li>

@endsection

@section('main_content')


    <div class="inner-container">
        <div class="toolbar">
            <ul class="has-divider-left">
                <li class="btn btn-default" href="{{route('admin.system-user.index')}}" act="link">
                    <i class="fa fa-user"></i>کاربران سیستمی
                </li>
                <li class="btn btn-default" href="{{route('admin.customer-user.index')}}" act="link">
                    <i class="fa fa-shopping-cart"></i>خریداران
                </li>
            </ul>
            <ul class="has-divider-left">
                @foreach(LayoutService::getLayoutMethods() as $layout_method)
                    <li href="{{route('admin.null')}}?layout_model=User&layout_method={{$layout_method["method"]}}"
                        act="link"
                        @if($layout_method["method"] == LayoutService::getRecord("User")->getMethod()) class="active" @endif>
                        <i class="fa {{$layout_method["icon"]}}"></i>
                    </li>
                @endforeach
            </ul>
            <ul>
                @foreach(SortService::getSortableFields('User') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=User&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
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

                @include('admin.pages.user.layout.'.LayoutService::getRecord("User")->getMethod())
            </div>
            <div class="fab-container">
                @include('admin.templates.buttons.fab-buttons', ['buttons' => ['create', 'download', 'upload']])
            </div>
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "User",
            "lastPage" => $users->lastPage(),
            "total" => $users->total(),
            "count" => $users->perPage(),
            "parentId" => $scope ?? null
        ])
    </div>
@endsection
