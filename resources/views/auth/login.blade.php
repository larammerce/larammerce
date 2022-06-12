@extends('auth.layout')

@section('form_attributes')
    method="POST" action="{{ url('/login') }}"
@endsection

@section('form_title')
    ورود به سامانه
@endsection

@section('form_body')

    <div class="form-group">
        <div class="form-material floating">
            <input class="form-control input-sm" type="text" value="{{old('username')}}" name="username">
            <label>نام کاربری</label>
        </div>
    </div>
    <div class="form-group">
        <div class="form-material floating">
            <input class="form-control input-sm" type="password" name="password">
            <label>رمز ورود</label>
        </div>
    </div>

    <div class="action-container">
        <button type="submit" id="login" class="btn float-left">ورود</button>
    </div>
@endsection