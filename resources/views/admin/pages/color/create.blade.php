@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.color.index')}}">رنگ ها</a></li>
    <li class="active"><a href="{{route('admin.color.create')}}">اضافه کردن رنگ</a></li>

@endsection

@section('form_title')اضافه کردن رنگ@endsection

@section('form_attributes') action="{{route('admin.color.store')}}" method="POST" enctype="multipart/form-data"  @endsection

@section('form_body')
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام رنگ</span>
        <input class="form-control input-sm" name="name" value="{{old('name')}}">
    </div>
    <div class="input-group group-sm col-lg-1 col-sm-2 col-md-2 col-xs-3">
        <span class="label">انتخاب رنگ</span>
        <input class="form-control input-sm" type="color" name="hex_code" value="{{old('hex_code')}}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <label>تصویر</label>
        <input class="form-control input-sm" name="image" type="file" multiple="true">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">کپشن</span>
        <input class="form-control input-sm" name="caption" value="{{old('caption')}}">
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
