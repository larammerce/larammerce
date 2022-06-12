@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.setting.index')}}">تنظیمات</a></li>
    <li class="active"><a href="{{route('admin.setting.create')}}">اضافه کردن تنظیم</a></li>

@endsection

@section('form_title')اضافه کردن تنظیم@endsection

@section('form_attributes') action="{{route('admin.setting.store')}}" method="POST" @endsection

@section('form_body')
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">کلید</span>
        <input class="form-control input-sm" name="key" value="{{ old('key') }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">مقدار</span>
        <textarea class="form-control input-sm" name="value" rows="3">{{ old('value') }}</textarea>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">شخصی&nbsp
            <input id="is_private" name="is_private" type="checkbox" value="1"
                   @if(old('is_private')) checked @endif/>
            <label for="is_private"></label>
        </span>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
