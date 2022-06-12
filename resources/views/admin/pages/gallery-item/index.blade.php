@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.gallery-item.index')}}">آیتم های گالری</a></li>
    <li class="active"><a href="{{route('admin.gallery-item.index')}}">لیست آیتم های گالری</a></li>
@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            @if(isset($gallery))
                <ul class="has-divider-left">
                    <li class="btn btn-default" href="{{route('admin.gallery.edit', $gallery)}}" act="link">
                        <i class="fa fa-photo"></i>بازگشت به گالری
                    </li>
                </ul>
            @endif
            <ul class="has-divider-left">
                @foreach(LayoutService::getLayoutMethods() as $layout_method)
                    <li href="{{route('admin.null')}}?layout_model=GalleryItem&layout_method={{$layout_method["method"]}}"
                        act="link"
                        @if($layout_method["method"] == LayoutService::getRecord("GalleryItem")->getMethod()) class="active" @endif>
                        <i class="fa {{$layout_method["icon"]}}"></i>
                    </li>
                @endforeach
            </ul>
            <ul>
                @foreach(SortService::getSortableFields('GalleryItem') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=GalleryItem&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
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
                @include('admin.pages.gallery-item.layout.'.LayoutService::getRecord("GalleryItem")->getMethod())
            </div>
            @if(isset($gallery))
                <div class="fab-container">
                    <div class="fab green">
                        <button act="link" href="{{route('admin.gallery-item.create')}}?gallery_id={{$gallery->id}}">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            @endif
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "GalleryItem",
            "lastPage" => $gallery_items->lastPage(),
            "total" => $gallery_items->total(),
            "count" => $gallery_items->perPage(),
            "parentId" => (isset($gallery) ? $gallery->id : $scope ?? null)
        ])
    </div>
@endsection
