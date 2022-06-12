@extends('admin.form_layout.col_8')

@section('bread_crumb')
    <li><a href="{{route('admin.invoice.index')}}">صورت حساب ها</a></li>
    <li class="active"><a href="{{route('admin.invoice.edit', $invoice)}}">ویرایش صورت حساب</a></li>

@endsection

@section('form_title')ویرایش صورت حساب@endsection

@section('form_attributes') action="{{route('admin.invoice.update', $invoice)}}" method="POST" @endsection

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
            <input class="form-control input-sm" name="payment_type" value="{{ $invoice->payment_type }}">
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
            <span class="label">وضعیت</span>
            <input class="form-control input-sm" name="status" value="{{ $invoice->status }}">
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">شناسه پرداخت</span>
            <input class="form-control input-sm" name="payment_id" value="{{ $invoice->payment_id }}">
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

        <a class="btn btn-sm btn-primary pull-left"
           href="{{route('admin.invoice-row.index')}}?invoice_id={{ $invoice->id }}">
            <i class="fa fa-line-chart"></i>
        </a>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
