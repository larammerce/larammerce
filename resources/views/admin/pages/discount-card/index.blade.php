@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.discount-group.index')}}">پلن های تخفیف</a></li>
    <li class="active">
        <a href="{{route('admin.discount-group.show', $discount_group)}}">{{$discount_group->title}}</a>
    <li class="active">
        <a href="{{route('admin.discount-card.index')}}?discount_group_id={{$discount_group->id}}">
            نمایش کارت های تخفیف
        </a>
    </li>
@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul>
                @foreach(SortService::getSortableFields('DiscountCard') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=DiscountCard&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
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
                @include('admin.pages.discount-card.layout.list')
            </div>
            <div class="fab-container">
                @php
                    $related_model_query_data = ["discount_group_id" => (isset($discount_group) ? $discount_group->id : $scope ?? null)];
                @endphp
                @include('admin.templates.buttons.fab-buttons', ['buttons' => ['create', 'download']])
            </div>
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "DiscountCard",
            "lastPage" => $discount_cards->lastPage(),
            "total" => $discount_cards->total(),
            "count" => $discount_cards->perPage(),
            "parentId" => (isset($discount_group) ? $discount_group->id : $scope ?? null)
        ])
    </div>
@endsection
