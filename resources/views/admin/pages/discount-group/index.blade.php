@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.discount-group.index')}}">پلن های تخفیف</a></li>
    <li class="active"><a href="{{route('admin.discount-group.index')}}">لیست پلن های تخفیف</a></li>

@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul>
                @foreach(SortService::getSortableFields('DiscountGroup') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=DiscountGroup&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
                        act="link">
                        @if($sortable_field->is_active)
                            <i class="fa {{$sortable_field->method == SortMethod::ASCENDING ? "fa-long-arrow-up" : "fa-long-arrow-down"}}"></i>
                        @endif
                        {{$sortable_field->title}}
                    </li>
                @endforeach
                <li class="btn btn-default" href="{{route('admin.discount-group.index')}}" act="link">
                    show all
                </li>
                <li class="btn btn-default" href="{{route('admin.discount-group.index')}}?deleted=true" act="link">
                    show deleted
                </li>
            </ul>
        </div>
        <div class="inner-container has-toolbar has-pagination">
            <div class="view-port">
                @include('admin.pages.discount-group.layout.list')
            </div>
            <div class="fab-container">
                <div class="fab green">
                    <button act="link" href="{{route('admin.discount-group.create')}}">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "DiscountGroup",
            "lastPage" => $discount_groups->lastPage(),
            "total" => $discount_groups->total(),
            "count" => $discount_groups->perPage(),
            "parentId" => $scope ?? null
        ])
    </div>
@endsection
