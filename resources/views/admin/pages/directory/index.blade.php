@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.directory.index')}}">پوشه ها</a></li>
    <li @if(!isset($directory)) class="active" @endif><a href="{{route('admin.directory.index')}}">لیست پوشه ها</a></li>
    @if(isset($directory))
        @foreach($directory->getParentDirectories() as $parent_directory)
            <li><a href="{{ $parent_directory->getAdminUrl() }}">{{ $parent_directory->title }}</a></li>
        @endforeach
        <script>window.directory_id = "{{$directory->id}}";</script>
    @endif

@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            @if(isset($directory))
                <ul class="has-divider-left">
                    @if($directory->directory_id)
                        <li act="link" href="{{route('admin.directory.show', $directory->directory_id)}}">
                            <i class="fa fa-level-up"></i>
                        </li>
                    @else
                        <li act="link" href="{{route('admin.directory.index')}}">
                            <i class="fa fa-level-up"></i>
                        </li>
                    @endif
                    <li class="btn btn-default" href="{{route('admin.directory.edit', $directory)}}" act="link">
                        <i class="fa fa-edit"></i>ویرایش
                    </li>
                    @if($directory->content_type == \App\Enums\Directory\DirectoryType::BLOG)
                        <li class="btn btn-default" href="{{route('admin.article.index')}}" act="link">
                            <i class="fa fa-file-text-o"></i>بلاگ‌ها
                        </li>
                    @elseif($directory->content_type == \App\Enums\Directory\DirectoryType::PRODUCT)
                        <li class="btn btn-default" href="{{route('admin.product.index')}}" act="link">
                            <i class="fa fa-cubes"></i>محصولات
                        </li>
                    @endif
                </ul>
            @endif
            <ul class="has-divider-left">
                @foreach(LayoutService::getLayoutMethods() as $layout_method)
                    <li href="{{route('admin.null')}}?layout_model=Directory&layout_method={{$layout_method["method"]}}"
                        act="link"
                        @if($layout_method["method"] == LayoutService::getRecord("Directory")->getMethod()) class="active" @endif>
                        <i class="fa {{$layout_method["icon"]}}"></i>
                    </li>
                @endforeach
            </ul>
            <ul class="has-divider-left">
                @foreach(SortService::getSortableFields('Directory') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=Directory&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
                        act="link">
                        @if($sortable_field->is_active)
                            <i class="fa {{$sortable_field->method == SortMethod::ASCENDING ?
                        "fa-long-arrow-up" : "fa-long-arrow-down"}}"></i>
                        @endif
                        {{$sortable_field->title}}
                    </li>
                @endforeach
            </ul>
            @if(isset($directory) and is_paste_possible($directory))
                <ul>
                    <li class="btn btn-default"
                        act="paste-file">
                        <i class="fa fa-paste"></i>بازنشانی
                    </li>
                </ul>
            @endif
        </div>
        <div class="inner-container has-toolbar has-pagination">
            <div class="view-port">

                @include('admin.pages.directory.layout.'.LayoutService::getRecord("Directory")->getMethod())
            </div>
            @if(!isset($directory) or (isset($directory) and $directory->url_part != ""))
                <div class="fab-container">
                    <div class="fab green">
                        <button act="link"
                                href="{{ route('admin.directory.appliances') }}@if(isset($directory))?id={{$directory->id}}@endif">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            @endif
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "Directory",
            "lastPage" => $directories->lastPage(),
            "total" => $directories->total(),
            "count" => $directories->perPage(),
            "parentId" => (isset($directory) ? $directory->id : $scope ?? null)
        ])
    </div>
@endsection
