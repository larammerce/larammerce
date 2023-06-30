@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.coupon.index')}}">رنگ ها</a></li>
    <li class="active"><a href="{{route('admin.coupon.edit', $coupon)}}">ویرایش رنگ</a></li>

@endsection

@section('form_title')ویرایش رنگ@endsection

@section('form_attributes') action="{{route('admin.coupon.update', $coupon)}}" method="POST" enctype="multipart/form-data" @endsection

@section('form_body')
    {{ method_field('PUT') }}
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">عنوان</span>
        <input class="form-control input-sm" name="title" value="{{old('title', $coupon->title)}}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">مبلغ</span>
        <input class="form-control input-sm" act="price" type="text" name="amount" value="{{old('amount', $coupon->amount)}}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">تاریخ انقضا</span>
        <input class="form-control input-sm" name="expire_at_datepicker" data-name="expire_at" value="{{old("expire_at", $coupon->expire_at->format("Y-m-d H:i:s"))}}">
        <input type="hidden" name="expire_at">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">شماره تماس کاربر</span>
        <input class="form-control input-sm" type="text" name="phone_number" value="{{old('phone_number', $coupon->customer->main_phone)}}">
    </div>

@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
