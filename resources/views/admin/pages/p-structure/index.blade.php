@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.p-structure.index')}}">قالب محصولات</a></li>
    <li class="active"><a href="{{route('admin.p-structure.index')}}">لیست قالب محصولات</a></li>

@endsection

@section('main_content')
    <script>window.PAGE_ID = "admin.pages.p-structure.index"</script>
    <div class="inner-container">
        <div class="toolbar">
            <ul class="has-divider-left">
                <li class="btn btn-default" href="{{route('admin.p-structure-attr-key.index')}}" act="link">
                    <i class="fa fa-key"></i>کلید ها
                </li>
                <li class="btn btn-default" href="{{route('admin.p-structure-attr-value.index')}}" act="link">
                    <i class="fa fa-navicon"></i>مقادیر
                </li>
                <li class="btn btn-default" href="{{route('admin.product.index')}}" act="link">
                    <i class="fa fa-cubes"></i>محصولات
                </li>
            </ul>
            <ul class="has-divider-left">
                @foreach(LayoutService::getLayoutMethods() as $layout_method)
                    <li href="{{route('admin.null')}}?layout_model=PStructure&layout_method={{$layout_method["method"]}}"
                        act="link"
                        @if($layout_method["method"] == LayoutService::getRecord("PStructure")->getMethod()) class="active" @endif>
                        <i class="fa {{$layout_method["icon"]}}"></i>
                    </li>
                @endforeach
            </ul>
            <ul>
                @foreach(SortService::getSortableFields('PStructure') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=PStructure&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
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
                @include('admin.pages.p-structure.layout.'.LayoutService::getRecord("PStructure")->getMethod())
            </div>
            <div class="fab-container">
                <div class="fab green">
                    <button act="link" href="{{route('admin.p-structure.create')}}">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "PStructure",
            "lastPage" => $p_structures->lastPage(),
            "total" => $p_structures->total(),
            "count" => $p_structures->perPage(),
            "parentId" => $scope ?? null
        ])
    </div>

@endsection

@section("outer_content")
    <div id="upload-p-structure-excel-file" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <form action="" method="post" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="modal-header">
                        <div class="button-container">
                            <div class="btn btn-exit" data-dismiss="modal"></div>
                        </div>
                        <div class="title-container"><h1 class="title"> آپلود فایل اکسل </h1></div>
                    </div>
                    <div class="modal-body">
                        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                            <label>انتخاب فایل اکسل</label>
                            <input class="form-control input-sm" name="file" type="file"
                                   accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-danger btn-default">تایید
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection
