@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.setting.index')}}">تنظیمات</a></li>
    <li class="active"><a href="{{route('admin.setting.create', compact('setting'))}}">ویرایش تنظیم</a></li>

@endsection

@section('form_title')ویرایش تنظیم@endsection

@section('form_attributes') action="{{route('admin.setting.update', $setting)}}" method="POST" form-with-hidden-checkboxes @endsection

@section('form_body')
    {{ method_field('PUT') }}
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">کلید</span>
        <input class="form-control input-sm" name="key" value="{{ $setting->key }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">مقدار</span>
        <textarea class="form-control input-sm" name="value" rows="3">{{ $setting->value }}</textarea>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">شخصی&nbsp
            <input id="is_private" name="is_private" type="checkbox" value="1"
                   @if($setting->user_id != null) checked @endif/>
            <label for="is_private"></label>
            <input id="is_private_hidden" name="is_private" type="hidden" value="0"/>
        </span>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
