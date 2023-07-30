@extends('admin.form_layout.col_8')

@section('bread_crumb')
    <li><a href="{{route('admin.invoice.index')}}">صورت حساب ها</a></li>
    <li class="active"><a href="{{route('admin.invoice.edit', $invoice)}}">ویرایش صورت حساب</a></li>

@endsection

@section('form_title')
    ویرایش صورت حساب
@endsection

@section('form_attributes')
    action="{{route('admin.invoice.update', $invoice)}}" method="POST"
@endsection

@section('form_body')
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        {{ method_field('PUT')}}
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">نام خیردار مربوطه</span>
            <input class="form-control input-sm" name="customer" value="{{ $invoice->customer->user->full_name }}"
                   disabled>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">نوع پرداخت</span>
            <select class="form-control input-sm" name="payment_type" disabled>
                @foreach(\App\Enums\Invoice\PaymentType::values() as $key)
                    <option value="{{ $key }}" @if($invoice->payment_type == $key) selected @endif>
                        {{ trans('invoice.payment_type.' . $key)}}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">وضعیت پرداخت</span>
            <select class="form-control input-sm" name="payment_status" disabled>
                @foreach(\App\Enums\Invoice\PaymentStatus::values() as $key)
                    <option value="{{ $key }}" @if($invoice->payment_status == $key) selected @endif>
                        {{ trans('invoice.payment_status.' . $key)}}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">وضعیت ارسال</span>
            <select class="form-control input-sm" name="shipment_status" disabled>
                @foreach(\App\Enums\Invoice\ShipmentStatus::values() as $key)
                    <option value="{{ $key }}" @if($invoice->shipment_status == $key) selected @endif>
                        {{ trans('invoice.shipment_status.' . $key)}}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            @if($invoice->shipment_status == \App\Enums\Invoice\ShipmentStatus::WAREHOUSE_EXIT_TAB)
                <a class="btn btn-sm btn-primary pull-left"
                   href="{{route('admin.invoice.show-shipment-sending', $invoice) }}">
                    تغییر وضعیت ارسال به
                    <b>در حال ارسال</b>
                </a>
            @elseif($invoice->shipment_status == \App\Enums\Invoice\ShipmentStatus::PREPARING_TO_SEND)
                <a class="btn btn-sm btn-primary pull-left"
                   href="{{route('admin.invoice.show-shipment-exit-tab', $invoice) }}">
                    تغییر وضعیت ارسال به
                    <b>مجوز خروج از انبار</b>
                </a>
            @elseif($invoice->shipment_status == \App\Enums\Invoice\ShipmentStatus::SENDING)
                <a class="btn btn-sm btn-primary pull-left"
                   href="{{route('admin.invoice.show-shipment-delivered', $invoice) }}">
                    تغییر وضعیت ارسال به
                    <b>تحویل به مشتری</b>
                </a>
            @endif
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">آدرس</span>
            <select class="form-control input-sm" name="customer_address_id">
                @foreach($invoice->customer->addresses as $address)
                    <option value="{{$address->id}}" @if($address->id == $invoice->customer_address_id) selected @endif>
                        {{ $address->name }} ({{$address->getFullAddress()}})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">کد پیگیری</span>
            <input class="form-control input-sm" name="tracking_code" value="{{ $invoice->tracking_code }}" disabled>
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <p>محصولات</p>
        <ul>
            @foreach($invoice->products as $product)
                <li><a href="{{route('admin.product.edit',$product)}}"><i
                                class="fa fa-eye"></i></a> {{ $product->title }} - کد محصول : {{ $product->code }}
                </li>
            @endforeach
        </ul>
        <div class="col-xs-12">
            <a class="btn btn-sm btn-primary pull-left"
               href="{{route('admin.invoice-row.index')}}?invoice_id={{ $invoice->id }}">
                <i class="fa fa-list-alt"></i>
            </a>
            <a class="btn btn-sm btn-success pull-left" href="#" disabled>
                <i class="fa fa-plus"></i>
            </a>
        </div>
        <div class="input-group with-icon with-unit group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12 pull-right">
            <span class="label">مجموع قیمت</span>
            <i class="fa fa-dollar"></i>
            <input class="form-control input-sm" name="sum" value="{{ $invoice->sum }}" act="price" disabled>
            <span class="unit">ریال</span>
        </div>

        <p>
            {!! $invoice->getCMIComment("<br/>")  !!}
        </p>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
