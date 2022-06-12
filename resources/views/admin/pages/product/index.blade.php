@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.product.index')}}">محصولات</a></li>
    <li class="active"><a href="{{route('admin.product.index')}}">لیست محصولات</a></li>
    @if(isset($directory))
        @foreach($directory->getParentDirectories() as $directory)
            <li><a href="{{ $directory->getAdminUrl() }}">{{ $directory->title }}</a></li>
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
                    <li class="btn btn-default" href="{{route('admin.directory.show-link-product', $directory)}}"
                        act="link">
                        <i class="fa fa-plus"></i>افزودن محصول مرتبط
                    </li>
                </ul>
            @elseif(isset($filter))
                <ul class="has-divider-left">
                    <li act="link" href="{{route('admin.product-filter.index')}}">
                        <i class="fa fa-level-up"></i>
                        بازگشت به لیست فیلترها
                    </li>
                </ul>
            @endif
            <ul class="has-divider-left">
                @foreach(LayoutService::getLayoutMethods() as $layout_method)
                    <li href="{{route('admin.null')}}?layout_model=Product&layout_method={{$layout_method["method"]}}"
                        act="link"
                        @if($layout_method["method"] == LayoutService::getRecord("Product")->getMethod()) class="active" @endif>
                        <i class="fa {{$layout_method["icon"]}}"></i>
                    </li>
                @endforeach
            </ul>
            <ul class="has-divider-left">
                @foreach(SortService::getSortableFields('Product') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=Product&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
                        act="link">
                        @if($sortable_field->is_active)
                            <i class="fa {{$sortable_field->method == SortMethod::ASCENDING ? "fa-long-arrow-up" : "fa-long-arrow-down"}}"></i>
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
                @include('admin.pages.product.layout.'.LayoutService::getRecord("Product")->getMethod())
            </div>
            @if(isset($directory))
                <div class="fab-container">
                    <div class="fab green">
                        <button act="link" href="{{route('admin.product.create')}}?directory_id={{$directory->id}}">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            @else
                <div class="fab-container">
                    @include('admin.templates.buttons.fab-buttons', ['buttons' => ['download', 'upload']])
                </div>
            @endif

        </div>
        @include('admin.templates.pagination', [
            "modelName" => "Product",
            "lastPage" => $products->lastPage(),
            "total" => $products->total(),
            "count" => $products->perPage(),
            "parentId" => (isset($directory) ? $directory->id :
            (isset($filter) ? $filter->id :
            ($scope ?? null)))
        ])
    </div>

@endsection

