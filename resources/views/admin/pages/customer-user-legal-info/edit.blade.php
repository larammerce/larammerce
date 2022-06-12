@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.customer-user.index')}}">خریداران</a></li>
    <li><a href="{{route('admin.customer-user.edit', $customer_user_legal_info->customer_user_id)}}">ویرایش خریدار</a>
    </li>
    <li class="active"><a href="{{route('admin.customer-user-legal-info.edit', $customer_user_legal_info)}}">ویرایش
            اطلاعات حقوقی خریدار</a></li>

@endsection

@section('form_title')ویرایش اطلاعات حقوقی خریدار@endsection

@section('form_attributes') action="{{route('admin.customer-user-legal-info.update', $customer_user_legal_info)}}" method="POST" @endsection

@section('form_body')
    {{ method_field('PUT')}}
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام شرکت</span>
        <input class="form-control input-sm" name="company_name" value="{{ $customer_user_legal_info->company_name }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">کد افتصادی</span>
        <input class="form-control input-sm" name="economical_code"
               value="{{ $customer_user_legal_info->economical_code }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">شناسه ملی</span>
        <input class="form-control input-sm" name="national_id" value="{{ $customer_user_legal_info->national_id }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">شماره ثبت</span>
        <input class="form-control input-sm" name="registration_code"
               value="{{ $customer_user_legal_info->registration_code }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">شماره تلفن ثابت</span>
        <input class="form-control input-sm" name="company_phone"
               value="{{ $customer_user_legal_info->company_phone }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">استان</span>
        <select class="form-control input-sm" name="state_id">
            @foreach(\App\Models\State::all() as $state)
                <option value="{{$state->id}}" @if($customer_user_legal_info->state_id == $state->id) selected @endif>
                    {{$state->name}}
                </option>
            @endforeach
        </select>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">شهر</span>
        <select class="form-control input-sm" name="city_id">
            @if($customer_user_legal_info->state_id != null)
                @foreach($customer_user_legal_info->state->cities as $city)
                    <option value="{{$city->id}}" @if($customer_user_legal_info->city_id == $city->id) selected @endif>
                        {{$city->name}}
                    </option>
                @endforeach
            @endif
        </select>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
