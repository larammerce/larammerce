@extends('admin.form_layout.layout')

@section('form_window')
    <div class="form-window-container col-lg-8 col-lg-offset-2 col-md-10 col-offset-md-1 col-sm-12 col-sm-offset-0 col-xs-12 col-xs-offset-0">
        <div class="form-window">
            <div class="top-bar">
                <div class="button-container">
                    <div class="btn btn-exit" act="link" href="{{history_back()}}"></div>
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
@stop
