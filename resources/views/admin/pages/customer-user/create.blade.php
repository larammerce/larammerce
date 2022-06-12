@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.customer-user.index')}}">خریداران</a></li>
    <li class="active"><a href="{{route('admin.customer-user.create') . "?id={$user->id}"}}">ایجاد خریدار</a></li>

@endsection

@section('form_title')ایجاد خریدار@endsection

@section('form_attributes') action="{{route('admin.customer-user.store')}}" method="POST" form-with-hidden-checkboxes @endsection

@section('form_body')

    <input type="hidden" name="user_id" value="{{ $user->id }}">
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">شماره تلفن</span>
        <input class="form-control input-sm" name="main_phone" value="{{ old('main_phone') }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">کد ملی</span>
        <input class="form-control input-sm" name="national_code" value="{{ old('national_code') }}">
    </div>
    <div class="input-group with-unit group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">اعتبار</span>
        <input class="form-control input-sm" name="credit" value="{{ old('credit') }}" act="price">
        <span class="unit">ریال</span>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">شخصیت حقوقی
            <input id="is_legal_person" name="is_legal_person" type="checkbox" value="1"
                   @if(old('is_legal_person')) checked @endif/>
            <label for="is_legal_person"></label>
            <input id="is_legal_person_hidden" name="is_legal_person" type="hidden" value="0"/>
        </span>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
