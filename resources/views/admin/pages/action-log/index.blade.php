@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.action-log.index')}}">لاگ ها</a></li>
    <li class="active"><a href="{{route('admin.action-log.index')}}">لیست لاگ ها</a></li>
@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul class="has-divider-left">
                <li class="btn btn-default" href="{{route('admin.user.index')}}" act="link">
                    <i class="fa fa-user"></i>کاربران
                </li>
            </ul>
            <ul class="has-divider-left">
                <li href="" act="button" data-toggle="modal" data-target="#filter-action-logs-modal">
                    <i class="fa fa-search"></i>
                    <span class="hidden-xs hidden-sm hidden-md">
                فیلتر
                    </span>
                </li>
            </ul>
            <ul class="has-divider-left">
                @foreach(LayoutService::getLayoutMethods() as $layoutMethod)
                    <li href="{{route('admin.null')}}?layout_model=ActionLog&layout_method={{$layoutMethod["method"]}}"
                        act="link"
                        @if($layoutMethod["method"] == LayoutService::getRecord("ActionLog")->getMethod()) class="active" @endif>
                        <i class="fa {{$layoutMethod["icon"]}}"></i>
                    </li>
                @endforeach
            </ul>
            <ul>
                @foreach(SortService::getSortableFields('ActionLog') as $sortableField)
                    <li class="btn btn-default {{$sortableField->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=ActionLog&sort_field={{$sortableField->field}}&sort_method={{$sortableField->method}}"
                        act="link">
                        @if($sortableField->is_active)
                            <i class="fa {{$sortableField->method == SortMethod::ASCENDING ? "fa-long-arrow-up" : "fa-long-arrow-down"}}"></i>
                        @endif
                        {{$sortableField->title}}
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="inner-container has-toolbar has-pagination">
            <div class="view-port">
                @include('admin.pages.action-log.layout.'.LayoutService::getRecord("ActionLog")->getMethod())
            </div>
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "ActionLog",
            "lastPage" => $action_logs->lastPage(),
            "total" => $action_logs->total(),
            "count" => $action_logs->perPage()
        ])
    </div>
@endsection

@include('admin.templates.modals.filter_action_logs_modal')
