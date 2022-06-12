@extends('admin.form_layout.layout')

@section('form_window')
    <div class="form-window-container col-lg-6 col-lg-offset-3 col-md-8 col-offset-md-2 col-sm-10 col-sm-offset-1 col-xs-12 col-xs-offset-0">
        <div class="form-window">
            <div class="top-bar">
                <div class="button-container">
                    <div class="btn btn-exit" act="link" href="{{history_back()}}"></div>
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
