@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.user.index')}}">کاربران</a></li>
    <li class="active"><a href="{{route('admin.user.create')}}">اضافه کردن کاربر</a></li>

@endsection

@section('form_title')اضافه کردن کاربر@endsection

@section('form_attributes') action="{{route('admin.user.update', $user)}}" method="POST" form-with-hidden-checkboxes @endsection

@section('form_body')
    {{ method_field('PUT')}}
    <input type="hidden" name="id" value="{{ $user->id }}">
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام</span>
        <input class="form-control input-sm" name="name"
               value="@if($errors->any()){{  old('name') }}@else{{ $user->name }}@endif">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام خانوادگی</span>
        <input class="form-control input-sm" name="family"
               value="@if($errors->any()){{  old('family') }}@else{{ $user->family }}@endif">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام کاربری</span>
        <input class="form-control input-sm" name="username"
               value="@if($errors->any()){{  old('username') }}@else{{ $user->username }}@endif">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">ایمیل</span>
        <input class="form-control input-sm" name="email" type="email"
               value="@if($errors->any()){{  old('email') }}@else{{ $user->email }}@endif">
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
        <span class="material-switch pull-right">کاربر سیستمی &nbsp
            <input id="is_system_user" name="is_system_user" type="checkbox" value="1"
                   @if($user->is_system_user) checked @endif/>
            <label for="is_system_user"></label>
            <input id="is_system_user_hidden" name="is_system_user" type="hidden" value="0"/>
        </span>
        <a class="btn btn-sm btn-primary {{!$user->is_system_user ? 'disabled' : ''}} pull-left"
           href="{{$user->is_system_user ? route('admin.system-user.edit', $user->systemUser) : '#'}}">
            ویرایش کاربر سیستمی
        </a>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">خریدار &nbsp
            <input id="is_customer_user" name="is_customer_user" type="checkbox" value="1"
                   @if($user->is_customer_user) checked @endif/>
            <label for="is_customer_user"></label>
            <input id="is_customer_user_hidden" name="is_customer_user" type="hidden" value="0"/>
        </span>
        <a class="btn btn-sm btn-primary pull-left {{!$user->is_customer_user ? 'disabled' : ''}}"
           href="{{$user->is_customer_user ? route('admin.customer-user.edit', $user->customerUser) : '#'}}">
            ویرایش خریدار
        </a>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
