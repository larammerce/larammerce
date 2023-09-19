@extends('admin.form_layout.layout')

@section('form_window')
    <div
        class="form-window-container col-lg-4 col-lg-offset-4 col-md-4 col-offset-md-4 col-sm-8 col-sm-offset-2 col-xs-12 col-xs-offset-0">
        <div class="form-window">
            <div class="top-bar">
                <div class="button-container">
                    @include("admin.form_layout.language_select")
                </div>
                <div class="title-container"><h1>@yield('form_title')</h1></div>
            </div>
            <div class="form-body">
                @yield('form_body')
                <div class="clearfix"></div>
            </div>
            <div class="form-footer">
                @yield('form_footer')
            </div>
        </div>
    </div>
@endsection
