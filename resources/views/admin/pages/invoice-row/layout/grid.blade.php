@foreach($invoice_rows as $invoice_row)
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 grid-item users">
        <div class="item-container">
            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-3 col">
                <div class="img-container">
                    <img class="img-responsive" src="/admin_dashboard/images/No_image.jpg.png">
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-2 col-xs-5 col">
                <div class="label">شناسه</div>
                <div>{{$invoice->id}}#</div>
            </div>
            <div class="col-lg-7 col-md-12 col-sm-12 col-xs-12 col">
                <div class="label">نام محصول</div>
                <div>{{$invoice_row->product->title}}</div>
            </div>
            <div class="col-lg-10 col-md-9 col-sm-6 col-xs-6 col">
                <div class="label">تعداد</div>
                <div>{{$invoice_row->count}}</div>
            </div>
            <div class="col-lg-6 col-md-9 col-sm-6 col-xs-6 col">
                <div class="label">تخفیف</div>
                <div>{{$invoice_row->discount_percentage}}%</div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col col-action">
                <div class="label">عملیات</div>
                <div class="actions-container">
                    <a class="btn btn-sm btn-primary" href="{{route('admin.invoice-row.edit', $invoice_row)}}">
                        <i class="fa fa-pencil"></i>
                    </a>
                    <a class="btn btn-sm btn-danger virt-form"
                       data-action="{{ route('admin.invoice-row.destroy', $invoice_row) }}"
                       data-method="DELETE" confirm>
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endforeach
