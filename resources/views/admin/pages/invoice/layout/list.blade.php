@foreach($invoices as $invoice)
    <div
        class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row roles @if(!$invoice->is_active) disabled @endif">
        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-3 col">
            <div class="label">شناسه</div>
            <div>{{$invoice->id}}#</div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 col">
            <div class="label">نام خریدار</div>
            <div>{{$invoice->customer->user->full_name}}</div>
        </div>
        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-3 col">
            <div class="label">تاریخ ثبت سفارش</div>
            <div><span class="label label-primary"
                       style="padding: 6px">{{TimeService::getDateTimeFrom($invoice->created_at)}}</span></div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 col">
            <div class="label">وضعیت پرداخت</div>
            <div>{{ trans('invoice.payment_status.' . $invoice->payment_status)}}</div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 col">
            <div class="label">وضعیت ارسال</div>
            <div>{{ trans('invoice.shipment_status.' . $invoice->shipment_status)}}</div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 col">
            <div class="label">مجموع قیمت</div>
            <div>{{\App\Utils\Common\Format::number($invoice->sum)}}</div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 col">
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
@endforeach
