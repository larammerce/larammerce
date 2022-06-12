@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.setting.appliances')}}">تنظیمات</a></li>
    <li class="active"><a href="{{route('admin.setting.action-log-setting.edit')}}">ویرایش تنظیمات لاگ گیری</a></li>

@endsection

@section('form_title')ویرایش تنظیمات لاگ گیری@endsection

@section('form_attributes') action="{{route('admin.setting.action-log-setting.update')}}" method="POST" form-with-hidden-checkboxes @endsection

@section('form_body')
    <script>window.HAS_CHECKBOX_INPUT = true;</script>
    {{ method_field('PUT') }}
    <h4>تنظیمات پیش‌فرض</h4>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">دوره لاگ گیری (تعداد روز)</span>
                <input class="form-control input-sm" type="text" name="log_period"
                       value="{{ $log_period }}" act="price">
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="material-switch pull-left">آیا سیستم لاگ گیری روشن است؟ &nbsp
                <input id="is-enabled" name="is_enabled" type="checkbox" value="1"
                       @if($is_enabled) checked @endif/>
                <label for="is-enabled"></label>
                <input id="is-enabled_hidden" name="is_enabled" type="hidden" value="0"/>
            </span>
            </div>
        </div>
    </div>
    <hr/>
    <div class="check-box-inputs-container" data-output-id="enabled-controllers">
        <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                <h4>انتخاب برای لاگ گیری</h4>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="material-switch pull-left">انتخاب همه &nbsp
                    <input id="check-all" class="checkbox-input-check-all" type="checkbox" value="1"/>
                    <label for="check-all"></label>
                    <input id="check-all_hidden" type="hidden" value="0"/>
                </span>
                </div>
            </div>
        </div>
        <br>
        @foreach($existing_controllers as $controller => $entity)
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                        <p id="entity-{{$entity}}">{{ trans("structures.classes.{$entity}") }}</p>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="material-switch pull-left ">
                    <input class="checkbox-input" id="{{$controller}}" type="checkbox" value="1"
                           @if(in_array($controller,$enabled_controllers)) checked @endif/>
                    <label class="checkbox-input-label" for="{{$controller}}"></label>
                    <input class="checkbox-input-hidden" id="{{$controller}}_hidden" type="hidden" value="0"/>
                </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <input type="hidden" id="enabled-controllers" name="enabled_controllers"
           value="">

@endsection


@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
