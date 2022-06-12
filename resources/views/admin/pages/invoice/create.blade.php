@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.invoice.index')}}">صورت حساب ها</a></li>
    <li class="active"><a href="{{route('admin.invoice.create')}}">اضافه کردن صورت حساب</a></li>

@endsection

@section('form_title')اضافه کردن صورت حساب@endsection

@section('form_attributes') action="{{route('admin.invoice.store')}}" method="POST" @endsection

@section('form_body')
    <input name="customer_user_id" type="hidden" value="{{ $customerUser->id }}">
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام خریدار مربوطه</span>
        <input class="form-control input-sm" name="customer" value="{{ $customerUser->user->full_name }}" disabled>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نوع پرداخت</span>
        <input class="form-control input-sm" name="payment_type" value="{{ old('payment_type') }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">آدرس</span>
        <select class="form-control input-sm" name="customer_address_id">
            @foreach($customerUser->addresses as $address)
                <option value="{{$address->id}}">
                    {{ $address->name }} ({{$address->getFullAddress()}})
                </option>
            @endforeach
        </select>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">وضعیت</span>
        <input class="form-control input-sm" name="status" value="{{ old('status') }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">شناسه پرداخت</span>
        <input class="form-control input-sm" name="payment_id" value="{{ old('payment_id') }}">
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
