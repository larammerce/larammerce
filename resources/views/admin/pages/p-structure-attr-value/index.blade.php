@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.p-structure-attr-value.index')}}">مقادیر ویژگی ها</a></li>
    <li class="active"><a href="{{route('admin.p-structure-attr-value.index')}}">لیست مقادیر ویژگی ها</a></li>

@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul class="has-divider-left">
                <li class="btn btn-default"
                    href="{{route('admin.p-structure.index')}}" act="link">
                    <i class="fa fa-wpforms"></i>
                    قالب ها
                </li>
                <li class="btn btn-default"
                    href="{{route('admin.p-structure-attr-key.index')}}" act="link">
                    <i class="fa fa-key"></i>
                    کلید ها
                </li>
            </ul>
            <ul>
                @foreach(SortService::getSortableFields('PStructureAttrValue') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=PStructureAttrValue&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
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
                @include('admin.pages.p-structure-attr-value.layout.list')
            </div>
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "PStructureAttrValue",
            "lastPage" => $p_structure_attr_values->lastPage(),
            "total" => $p_structure_attr_values->total(),
            "count" => $p_structure_attr_values->perPage(),
            "parentId" => (request()->has("p_structure_attr_key_id") ? request()->get('p_structure_attr_key_id') : $scope ?? null)
        ])
    </div>
@endsection
