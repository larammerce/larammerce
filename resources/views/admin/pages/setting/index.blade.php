@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.setting.index')}}">تنظیمات</a></li>
    <li class="active"><a href="{{route('admin.setting.index')}}">لیست تنظیمات</a></li>

@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul>
                @foreach(LayoutService::getLayoutMethods() as $layout_method)
                    <li href="{{route('admin.null')}}?layout_model=FeatureConfig&layout_method={{$layout_method["method"]}}"
                        act="link"
                        @if($layout_method["method"] == LayoutService::getRecord("FeatureConfig")->getMethod()) class="active" @endif>
                        <i class="fa {{$layout_method["icon"]}}"></i>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="inner-container has-toolbar has-pagination">
            <div class="view-port">
                @include('admin.pages.setting.layout.'.LayoutService::getRecord("FeatureConfig")->getMethod())
            </div>
            <div class="fab-container">
                <div class="fab green">
                    <button act="link" href="{{route('admin.setting.create')}}">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "FeatureConfig",
            "lastPage" => $settings->lastPage(),
            "total" => $settings->total(),
            "count" => $settings->perPage(),
            "parentId" => $scope ?? null
        ])
    </div>
@endsection
