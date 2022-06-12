@extends('admin.form_layout.col_8')

@section('bread_crumb')
    <li><a href="{{route('admin.invoice.index')}}">صورت حساب ها</a></li>
    <li class="active"><a href="{{route('admin.invoice.edit', $invoice)}}">تغییر وضعیت سفارش به تحویل به مشتری</a></li>

@endsection

@section('form_title')تغییر وضعیت سفارش به تحویل به مشتری@endsection

@section('form_attributes') action="{{route('admin.invoice.set-shipment-delivered', $invoice)}}" method="POST" @endsection

@section('form_body')
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">کد پیگیری</span>
            <input class="form-control input-sm" name="tracking_code" value="{{ $invoice->tracking_code }}" disabled>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="material-switch pull-right">  ارسال پیامک برای مشتری
                <input id="notify_customer" name="notify_customer" type="checkbox" value="1" checked/>
                <label for="notify_customer"></label>
            </span>
        </div>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
