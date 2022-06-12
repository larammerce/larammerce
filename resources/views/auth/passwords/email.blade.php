@extends('auth.layout')

@section('form_attributes')
    method="POST" action="{{ url('/password/email') }}"
@endsection

@section('form_title')
    فراموشی رمز عبور
@endsection

@section('form_body')
    <div class="form-message-container col-xs-12 col-sm-12 col-md-12 col-lg-12">
        @if(isset($token_saved))
            <p class="alert alert-success">ایمیل بازیابی کلمه عبور با موفقیت ارسال شد.</p>
        @elseif(isset($invalid_token))
            <p class="alert alert-danger">لینک مورد نظر فاقد اعتبار است، لطفا مجددا امتحان کنید.</p>
        @elseif(isset($repeated_request))
            <p class="alert alert-danger">ایمیل بازیابی کلمه عبور قبلا برای شما ارسال شده، لطفا ایمیل خود را چک
                کنید. (زمان باقی‌مانده برای درخواست بعدی: {{ $remaining_minutes }} دقیقه)</p>
        @endif
    </div>
    <div class="input-group with-icon group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">ایمیل</span>
        <i class="fa fa-user"></i>
        <input class="form-control input-sm" type="text" value="{{old('email')}}" name="email">
    </div>
    <div class="action-container col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <button class="btn btn-sm btn-default">ارسال لینک تغییر کلمه عبور</button>
    </div>
@endsection