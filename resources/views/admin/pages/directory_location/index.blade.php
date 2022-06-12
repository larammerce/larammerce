@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.directory-location.index')}}">مدیریت محدودیت فروش</a></li>
    <li class="active"><a href="{{route('admin.directory-location.index')}}">لیست ناحیه ها</a></li>

@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul>
                @foreach(SortService::getSortableFields('DirectoryLocation') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=DirectoryLocation&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
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
                @include('admin.pages.directory_location.layout.'.LayoutService::getRecord("DirectoryLocation")->getMethod())
            </div>
            @if(isset($directory))
                <div class="fab-container">
                    <div class="fab green">
                        <button act="link"
                                href="{{route('admin.directory-location.create')}}?directory_id={{$directory->id}}">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            @endif
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "DirectoryLocation",
            "lastPage" => $directory_locations->lastPage(),
            "total" => $directory_locations->total(),
            "count" => $directory_locations->perPage(),
            "parentId" => (isset($directory) ? $directory->id : $scope ?? null)
        ])
    </div>
@endsection
