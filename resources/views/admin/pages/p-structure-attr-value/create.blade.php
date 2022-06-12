@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.p-structure-attr-value.index')}}">مقادیر ویژگی ها</a></li>
    <li class="active"><a href="{{route('admin.p-structure-attr-value.create')}}">اضافه کردن مقدار جدید</a></li>

@endsection

@section('form_title')اضافه کردن مقدار@endsection

@section('form_attributes')
    action="{{route('admin.p-structure-attr-value.store')}}" method="POST" enctype="multipart/form-data"
@endsection

@section('form_body')
    <input class="form-control input-sm" type="hidden" name="p_structure_attr_key_id" value="{{$p_structure_attr_key->id}}">
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام کلید</span>
        <input class="form-control input-sm" type="text" value="{{$p_structure_attr_key->title}}" disabled>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام</span>
        <input class="form-control input-sm" name="name" value="{{old('name')}}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام اختصار انگلیسی</span>
        <input class="form-control input-sm" name="en_name" value="{{old('en_name')}}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <label>تصویر</label>
        <input class="form-control input-sm" name="image" type="file" multiple="true">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">alias تصویر</span>
        <input class="form-control input-sm" name="image_alias" value="{{old('image_alias')}}">
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
