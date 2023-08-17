@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.invoice.index')}}">صورت حساب ها</a></li>
    <li class="active"><a href="{{route('admin.invoice.index')}}">لیست صورت حساب ها</a></li>
@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul class="has-divider-left">
                <li class="btn btn-default" href="{{route('admin.customer-user.index')}}" act="link">
                    <i class="fa fa-shopping-cart"></i>خریداران
                </li>
                <li class="btn btn-default" href="{{route('admin.customer-address.index')}}" act="link">
                    <i class="fa fa-location-arrow"></i>آدرس خریداران
                </li>
            </ul>
            <ul class="has-divider-left">
                @foreach(LayoutService::getLayoutMethods() as $layout_method)
                    <li href="{{route('admin.null')}}?layout_model=Invoice&layout_method={{$layout_method["method"]}}"
                        act="link"
                        @if($layout_method["method"] == LayoutService::getRecord("Invoice")->getMethod()) class="active" @endif>
                        <i class="fa {{$layout_method["icon"]}}"></i>
                    </li>
                @endforeach
            </ul>
            <ul>
                @foreach(SortService::getSortableFields('Invoice') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=Invoice&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
                        act="link">
                        @if($sortable_field->is_active)
                            <i class="fa {{$sortable_field->method == SortMethod::ASCENDING ? "fa-long-arrow-up" : "fa-long-arrow-down"}}"></i>
                        @endif
                        {{$sortable_field->title}}
                    </li>
                @endforeach
                @if($show_filtered)
                    <li class="btn btn-default" href="{{route('admin.invoice.index')}}" act="link">
                        show all
                    </li>

                @else
                    {{-- <li class="btn btn-default" href="{{route('admin.invoice.index')}}?filtered=true" act="link">
                        show filtered
                    </li> --}}
                    <li class="btn btn-default" href="{{route('admin.invoice.filter')}}" act="link">
                        show filtered
                    </li>
                @endif
            </ul>
        </div>
        <div class="inner-container has-toolbar has-pagination">
            <div class="view-port">
                @include('admin.pages.invoice.layout.'.LayoutService::getRecord("Invoice")->getMethod())
            </div>
            @if(isset($customerUser))
                <div class="fab-container">
                    <div class="fab green">
                        <button act="link"
                                href="{{route('admin.invoice.create')}}?customer_user_id={{$customerUser->id}}">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            @else
                <div class="fab-container">
                    @include('admin.templates.buttons.fab-buttons', ['buttons' => ['download']])
                </div>
            @endif
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "Invoice",
            "lastPage" => $invoices->lastPage(),
            "total" => $invoices->total(),
            "count" => $invoices->perPage(),
            "parentId" => $scope ?? null
        ])
    </div>
@endsection
