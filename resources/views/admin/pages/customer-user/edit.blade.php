@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.customer-user.index')}}">خریداران</a></li>
    <li class="active"><a href="{{route('admin.customer-user.edit', $customer_user)}}">ویرایش خریدار</a></li>

@endsection

@section('form_title')ویرایش خریدار@endsection

@section('form_attributes') action="{{route('admin.customer-user.update', $customer_user)}}" method="POST" form-with-hidden-checkboxes @endsection

@section('form_body')
    {{ method_field('PUT')}}
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">شماره تلفن</span>
        <input class="form-control input-sm" name="main_phone" value="{{ $customer_user->main_phone }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">کد ملی</span>
        <input class="form-control input-sm" name="national_code" value="{{ $customer_user->national_code }}">
    </div>
    <div class="input-group with-unit group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">اعتبار</span>
        <input class="form-control input-sm" name="credit" value="{{ $customer_user->credit }}" act="price">
        <span class="unit">ریال</span>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">شماره کارت</span>
        <input class="form-control input-sm" name="bank_account_card_number"
               value="{{ $customer_user->bank_account_card_number }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">شماره شبا</span>
        <input class="form-control input-sm" name="bank_account_uuid" value="{{ $customer_user->bank_account_uuid }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">شخصیت حقوقی
            <input id="is_legal_person" name="is_legal_person" type="checkbox" value="1"
                   @if($customer_user->is_legal_person) checked @endif/>
            <label for="is_legal_person"></label>
            <input id="is_legal_person_hidden" name="is_legal_person" type="hidden" value="0"/>
        </span>
    </div>
    <hr>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <a class="btn btn-sm btn-primary {{!$customer_user->is_legal_person ? 'disabled' : ''}} pull-left"
           href="{{$customer_user->is_legal_person ? route('admin.customer-user-legal-info.edit', $customer_user->legalInfo) : '#'}}">
            ویرایش اطلاعات حقوقی
        </a>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <a class="btn btn-sm btn-primary pull-left"
           href="{{route('admin.invoice.index')}}?customer_user_id={{$customer_user->id}}">
            لیست صورت حساب ها
        </a>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <a class="btn btn-sm btn-primary pull-left"
           href="{{route('admin.customer-address.index')}}?customer_user_id={{$customer_user->id}}">
            لیست آدرس ها
        </a>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
