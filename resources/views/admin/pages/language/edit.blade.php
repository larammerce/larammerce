@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.setting.appliances')}}">تنظیمات</a></li>
    <li class="active"><a href="{{route('admin.setting.language.edit')}}">ویرایش تنظیمات سیستم چند زبانه</a></li>
@endsection

@section('form_title')ویرایش تنظیمات سیستم چند زبانه@endsection

@section('form_attributes') action="{{route('admin.setting.language.update')}}" method="POST" form-with-hidden-checkboxes @endsection

@section('form_body')
    <script>window.PAGE_ID = "admin.pages.language.edit";</script>
    {{ method_field('PUT') }}
    <div class="accordion" id="accordion">
        @foreach($languages as $lang_id => $lang_config)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#lang_{{$lang_id}}">
                            @if($lang_config->is_default)
                                <i class="fa fa-check-circle"></i>
                            @endif
                            @lang('language.id.'.$lang_id)@if($lang_config->is_enabled) (فعال) @endif
                        </a>
                    </h4>
                </div>
                <div id="lang_{{$lang_id}}" class="panel-collapse collapse">
                    <div class="panel-body">
                        @foreach($lang_config->getInputData() as $input_name => $input_data)
                            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                                @if($input_data["type"] == "checkbox")
                                    <span class="material-switch pull-right">
                                        @lang('language.config.'.$input_name) ؟ &nbsp
                                        <input id="languages[{{$lang_id}}][{{$input_name}}]"
                                               name="languages[{{$lang_id}}][{{$input_name}}]"
                                               type="checkbox" value="1" @if($lang_config->$input_name) checked @endif
                                        />
                                        <label for="languages[{{$lang_id}}][{{$input_name}}]"></label>
                                        <input id="languages[{{$lang_id}}][{{$input_name}}]_hidden"
                                               name="languages[{{$lang_id}}][{{$input_name}}]" type="hidden" value="0"/>
                                    </span>
                                @else
                                    <span class="label">@lang('languages.config.'.$input_name)</span>
                                    <input type="{{$input_data["type"]}}"
                                           name="languages[{{$lang_id}}][{{$input_name}}]"
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

@section('floating_action_button')
    <div class="fab-container">
        <div class="fab green" style="margin:4rem auto 4rem auto;">
            <button act="link" href="{{route('admin.setting.language.item.create')}}">
                <i class="fa fa-plus"></i>
            </button>
        </div>
    </div>
@endsection
