@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.setting.appliances')}}">تنظیمات</a></li>
    <li class="active"><a href="{{route('admin.setting.payment-driver.edit')}}">ویرایش تنظیمات درگاه های پرداخت</a></li>
@endsection

@section('form_title')ویرایش تنظیمات درگاه آنلاین پرداخت@endsection

@section('form_attributes') action="{{route('admin.setting.payment-driver.update')}}" method="POST" enctype="multipart/form-data" form-with-hidden-checkboxes @endsection

@section('form_body')
    <script>window.PAGE_ID = "admin.pages.payment-driver.edit";</script>
    {{ method_field('PUT') }}
    <div class="accordion" id="accordion">
        @foreach($drivers as $driver_id => $driver_config)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#driver_{{$driver_id}}">
                            @if($driver_config->is_default)
                                <i class="fa fa-check-circle"></i>
                            @endif
                            @lang('payment.drivers.'.$driver_id)@if($driver_config->is_enabled) (فعال) @endif
                        </a>
                    </h4>
                </div>
                <div id="driver_{{$driver_id}}" class="panel-collapse collapse">
                    <div class="panel-body">
                        <img src="{{ $driver_config->getLogoPath() }}" style="height: 70px;">
                        @foreach($driver_config->getInputData() as $input_name => $input_data)
                            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                                @if($input_data["type"] == "checkbox")
                                {{--TODO: whenever is_default is checked it must be unchecked for others.--}}
                                    <span class="material-switch pull-right">
                                        @lang('payment.config.'.$input_name) ؟ &nbsp
                                        <input id="drivers[{{$driver_id}}][{{$input_name}}]"
                                               name="drivers[{{$driver_id}}][{{$input_name}}]"
                                               type="checkbox" value="1" @if($driver_config->$input_name) checked @endif/>
                                        <label for="drivers[{{$driver_id}}][{{$input_name}}]"></label>
                                        <input id="drivers[{{$driver_id}}][{{$input_name}}]_hidden"
                                               name="drivers[{{$driver_id}}][{{$input_name}}]" type="hidden" value="0"/>
                                    </span>
                                @elseif($input_data["type"] == "file")
                                    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                                        <label>@lang('payment.config.'.$input_name)</label>
                                        @if(!$driver_config->hasFile())
                                            <input class="form-control input-sm" name="drivers[{{$driver_id}}][{{$input_name}}]"
                                                   type="file" multiple="true">
                                        @else
                                            <div class="image-container">
                                                <a href="{{ route('admin.setting.payment-driver.remove-file', $driver_id) }}"
                                                   class="btn btn-sm btn-danger btn-remove">حذف فایل</a>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="label">@lang('payment.config.'.$input_name)</span>
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
