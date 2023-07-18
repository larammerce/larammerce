@extends('admin.form_layout.col_10')

@section('bread_crumb')
    <li><a href="{{route('admin.setting.appliances')}}">تنظیمات</a></li>
    <li class="active"><a href="{{route('admin.setting.env-file.edit')}}">تنظیمات فایل .env</a></li>

@endsection

@section('form_title')
    تنظیمات فایل .env
@endsection

@section('form_attributes')
    action="{{route('admin.setting.env-file.update')}}" method="POST" form-with-hidden-checkboxes
@endsection

@section('form_body')
    <script>window.PAGE_ID = "admin.pages.env-file.edit";</script>
    {{ method_field('PUT') }}
    <h4>کلیدهای موجود</h4>
    <hr>
    @php
        $counter = 0;
    @endphp
    <div id="env-list">
        @foreach($env_vars as $key => $value)
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                        <span class="label">کلید - {{$counter + 1}}</span>
                        <input class="form-control input-sm" type="text" dir="ltr" name="env_rows[{{$counter}}][key]"
                               value="{{ $key }}">
                    </div>
                </div>
                <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
                    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                    <span class="label">مقدار - {{$counter + 1}}  -
                        @if(in_array($key, $deprecated_keys))
                            <i style="color: orangered">این متغیر دیگر در پلتفرم کاربردی ندارد و می‌توانید آن را پاک کنید.</i>
                        @endif
                    </span>
                        <input class="form-control input-sm" type="text" dir="ltr" name="env_rows[{{$counter}}][value]"
                               value="{{ $value }}">
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                    <button class="btn btn-sm btn-danger delete-row" type="button">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
            @php
                $counter++;
            @endphp
        @endforeach
        <br>
        <br>
        <h4>کلیدهایی که بایستی اضافه گردند</h4>
        <hr>
        @foreach($missing_vars as $key => $value)
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                        <span class="label">کلید - {{$counter + 1}}</span>
                        <input class="form-control input-sm" type="text" dir="ltr" name="env_rows[{{$counter}}][key]"
                               value="{{ $key }}">
                    </div>
                </div>
                <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
                    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                        <span class="label">مقدار - {{$counter + 1}}</span>
                        <input class="form-control input-sm" type="text" dir="ltr" name="env_rows[{{$counter}}][value]"
                               value=" ">
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                    <button class="btn btn-sm btn-danger delete-row" type="button">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
            @php
                $counter++;
            @endphp
        @endforeach
    </div>

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <button type="button" class="btn btn-block btn-success" id="add-row">
                <i class="fa fa-plus"></i>
            </button>
        </div>
    </div>

    <script>
        window.envRowCounter = {{$counter + 1}};
    </script>
@endsection


@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
