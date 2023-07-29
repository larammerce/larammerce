@foreach($invoices as $invoice)
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 grid-item users">
        <div class="item-container">
            <div class="col-lg-5 col-md-4 col-sm-3 col-xs-5 col">
                <div class="label">شناسه</div>
                <div>{{$invoice->id}}#</div>
            </div>
            <div class="col-lg-7 col-md-8 col-sm-5 col-xs-7 col">
                <div class="label">نام خریدار</div>
                <div>{{$invoice->customer->user->full_name}}</div>
            </div>
            <div class="col-lg-5 col-md-4 col-sm-4 col-xs-6 col">
                <div class="label">نوع پرداخت</div>
                <div>{{ trans('invoice.payment_type.' . $invoice->payment_type)}}</div>
            </div>
            <div class="col-lg-7 col-md-8 col-sm-6 col-xs-6 col">
                <div class="label">وضعیت پرداخت</div>
                <div>{{ trans('invoice.payment_status.' . $invoice->payment_status)}}</div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-6 col-xs-12 col">
                <div class="label">وضعیت ارسال</div>
                <div>{{ trans('invoice.shipment_status.' . $invoice->shipment_status)}}</div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col">
                <div class="label">مجموع قیمت</div>
                <div>{{format_price($invoice->sum)}}</div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col col-action">
                <div class="label">عملیات</div>
                <div class="actions-container">
                    <a class="btn btn-sm btn-primary" href="{{route('admin.invoice.edit', $invoice)}}">
                        <i class="fa fa-pencil"></i>
                    </a>
                    <a class="btn btn-sm btn-danger virt-form"
                       data-action="{{ route('admin.invoice.destroy', $invoice) }}"
                       data-method="DELETE" confirm>
                        <i class="fa fa-trash"></i>
                    </a>
                    <a class="btn btn-sm btn-success" href="{{route('admin.invoice.show', $invoice)}}">
                        <i class="fa fa-eye"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endforeach