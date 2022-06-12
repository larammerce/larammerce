@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.customer-meta-category.index')}}">متادیتای کاربران</a></li>
    <li class="active"><a href="{{route('admin.customer-meta-category.index')}}">لیست موارد</a></li>
@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul>
                @foreach(SortService::getSortableFields('CustomerMetaCategory') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=CustomerMetaCategory&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
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
                @include('admin.pages.customer-meta-category.layout.list')
            </div>
            <div class="fab-container">
                <div class="fab green">
                    <button act="link"
                            href="{{route('admin.customer-meta-category.create')}}">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "CustomerMetaCategory",
            "lastPage" => $customer_meta_categories->lastPage(),
            "total" => $customer_meta_categories->total(),
            "count" => $customer_meta_categories->perPage(),
            "parentId" => $scope ?? null
        ])
    </div>
@endsection
