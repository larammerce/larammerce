@extends('auth.layout')

@section('form_attributes')
    method="POST" action="{{ url('/password/reset') }}"
@endsection

@section('form_title')
    تغییر کلمه‌ی عبور
@endsection

@section('form_body')
    @if(isset($token))
        <input type="hidden" name="token" value="{{ $token }}">
    @endif
    @if(isset($email))
        <input type="hidden" name="email" value="{{ $email }}">
    @endif
    <div class="form-message-container">
        @if($errors->has('email'))
            <div class="alert alert-danger" style="display: flex;direction: rtl;">{{ $errors->first('email') }}</div>
        @endif
    </div>
    <div class="input-group with-icon group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">کلمه‌ی عبور جدید</span>
        <i class="fa fa-user"></i>
        <input class="form-control input-sm" type="password" name="password">
    </div>
    <div class="input-group with-icon group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">تکرار کلمه‌ی عبور جدید</span>
        <i class="fa fa-key"></i>
        <input class="form-control input-sm" type="password" name="password_confirmation">
    </div>
    <div class="action-container col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <button class="btn btn-sm btn-default">تغییر کلمه‌ی عبور</button>
    </div>

@endsection
