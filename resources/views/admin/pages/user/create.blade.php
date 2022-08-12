@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.user.index')}}">کاربران</a></li>
    <li class="active"><a href="{{route('admin.user.create')}}">اضافه کردن کاربر</a></li>

@endsection

@section('form_title')اضافه کردن کاربر@endsection

@section('form_attributes') action="{{route('admin.user.store')}}" method="POST" form-with-hidden-checkboxes  @endsection

@section('form_body')

    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام</span>
        <input class="form-control input-sm" name="name" value="{{ old('name') }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام خانوادگی</span>
        <input class="form-control input-sm" name="family" value="{{ old('family') }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام کاربری</span>
        <input class="form-control input-sm" name="username" value="{{ old('username') }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">ایمیل</span>
        <input class="form-control input-sm" name="email" type="email" value="{{ old('email') }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">کلمه عبور</span>
        <input class="form-control input-sm" name="password" type="password">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">تکرار کلمه عبور</span>
        <input class="form-control input-sm" name="password_confirmation" type="password">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نوع معرف</span>
        <input class="form-control input-sm" name="representative_type" type="text">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام کاربری معرف</span>
        <input class="form-control input-sm" name="representative_username" type="text">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">کاربر سیستمی
            <input id="is_system_user" name="is_system_user" type="checkbox" value="1"
                   @if(old('is_system_user')) checked @endif/>
            <label for="is_system_user"></label>
            <input id="is_system_user_hidden" name="is_system_user" type="hidden" value="0"/>
        </span>
        <span class="material-switch pull-right">خریدار
            <input id="is_customer_user" name="is_customer_user" type="checkbox" value="1"
                   @if(old('is_customer_user')) checked @endif/>
            <label for="is_customer_user"></label>
            <input id="is_customer_user_hidden" name="is_customer_user" type="hidden" value="0"/>
        </span>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
