@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.setting.appliances')}}">تنظیمات</a></li>
    <li class="active"><a href="{{route('admin.setting.financial-driver.edit')}}">ویرایش تنظیمات سیستم مدیریت مالی و انبارداری</a></li>
@endsection

@section('form_title')ویرایش تنظیمات سیستم مدیریت مالی و انبارداری@endsection

@section('form_attributes') action="{{route('admin.setting.financial-driver.update')}}" method="POST" enctype="multipart/form-data" form-with-hidden-checkboxes @endsection

@section('form_body')
    <script>window.PAGE_ID = "admin.pages.financial-driver.edit";</script>
    {{ method_field('PUT') }}
    <div class="accordion" id="accordion">
        @foreach($drivers as $driver_id => $driver_config)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#driver_{{$driver_id}}">
                            @if($driver_config->is_enabled)
                                <i class="fa fa-check-circle"></i>
                            @endif
                            @lang('finance.drivers.'.$driver_id)
                        </a>
                    </h4>
                </div>
                <div id="driver_{{$driver_id}}" class="panel-collapse collapse">
                    <div class="panel-body">
                        @foreach($driver_config->getInputData() as $input_name => $input_data)
                            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                                @if($input_data["type"] == "checkbox")
                                    <span class="material-switch pull-right">
                                        @lang('finance.config.'.$input_name) ؟ &nbsp
                                        <input id="drivers[{{$driver_id}}][{{$input_name}}]"
                                               name="drivers[{{$driver_id}}][{{$input_name}}]"
                                               type="checkbox" value="1" @if($driver_config->$input_name) checked @endif/>
                                        <label for="drivers[{{$driver_id}}][{{$input_name}}]"></label>
                                        <input id="drivers[{{$driver_id}}][{{$input_name}}]_hidden"
                                               name="drivers[{{$driver_id}}][{{$input_name}}]" type="hidden" value="0"/>
                                    </span>
                                @else
                                    <span class="label">@lang('finance.config.'.$input_name)</span>
                                    <input type="{{$input_data["type"]}}"
                                           name="drivers[{{$driver_id}}][{{$input_name}}]"
                                           value="{{$input_data["value"]}}"
                                           class="form-control input-sm">
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection


@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
