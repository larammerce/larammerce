@extends('admin.layout')

@section('extra_style')
@stop

@section('main_content')
    <div class="form-layout-container">
        <form @yield('form_attributes') act="main-form">
            {{ csrf_field() }}
            @if(session()->has('file_exception'))
                <p class="alert alert-danger"><strong> خطا:</strong> اندازه‌ی فایل آپلود شده بیش از حد مجاز است. </p>
            @endif
            @yield('form_window')
        </form>
    </div>
@stop
