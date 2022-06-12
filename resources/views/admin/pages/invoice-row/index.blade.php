@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.invoice-row.index')}}">صورت حساب ها</a></li>
    <li class="active"><a href="{{route('admin.invoice-row.index')}}">لیست صورت حساب ها</a></li>
@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            @if(isset($invoice))
                <ul class="has-divider-left">
                    <li class="btn btn-default" href="{{route('admin.invoice.edit', $invoice)}}" act="link">
                        <i class="fa fa-file-o"></i>بازگشت به فاکتور
                    </li>
                </ul>
            @endif
            <ul class="has-divider-left">
                @foreach(LayoutService::getLayoutMethods() as $layout_method)
                    <li href="{{route('admin.null')}}?layout_model=InvoiceRow&layout_method={{$layout_method["method"]}}"
                        act="link"
                        @if($layout_method["method"] == LayoutService::getRecord("InvoiceRow")->getMethod()) class="active" @endif>
                        <i class="fa {{$layout_method["icon"]}}"></i>
                    </li>
                @endforeach
            </ul>
            <ul>
                @foreach(SortService::getSortableFields('InvoiceRow') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=InvoiceRow&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
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
                @include('admin.pages.invoice-row.layout.'.LayoutService::getRecord("InvoiceRow")->getMethod())
            </div>
            @if(isset($invoice))
                <div class="fab-container">
                    <div class="fab green">
                        <button act="link" href="{{route('admin.invoice-row.create')}}?invoice_id={{$invoice->id}}"
                                disabled>
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            @endif
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "InvoiceRow",
            "lastPage" => $invoice_rows->lastPage(),
            "total" => $invoice_rows->total(),
            "count" => $invoice_rows->perPage(),
            "parentId" => (isset($invoice) ? $invoice->id : $scope ?? null)
        ])
    </div>
@endsection
