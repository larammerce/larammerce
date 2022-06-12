@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.p-structure-attr-value.index')}}">مقادیر ویژگی ها</a></li>
    <li class="active"><a href="{{route('admin.p-structure-attr-value.edit', $p_structure_attr_value)}}">ویرایش مقدار</a></li>

@endsection

@section('form_title')ویرایش مقدار@endsection

@section('form_attributes')
    action="{{route('admin.p-structure-attr-value.update', $p_structure_attr_value)}}" method="POST"  enctype="multipart/form-data"
@endsection

@section('form_body')
    {{ method_field('PUT') }}
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام کلید</span>
        <input class="form-control input-sm" type="text" value="{{$p_structure_attr_value->key->title}}" disabled>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام</span>
        <input class="form-control input-sm" name="name" value="{{$p_structure_attr_value->name}}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام اختصار انگلیسی</span>
        <input class="form-control input-sm" name="en_name" value="{{$p_structure_attr_value->en_name}}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">اولویت</span>
        <input class="form-control input-sm" name="priority" value="{{$p_structure_attr_value->priority}}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <label>تصویر</label>
        @if(!$p_structure_attr_value->hasImage())
            <input class="form-control input-sm" name="image" type="file" multiple="true">
        @else
            <div class="photo-container">
                <a href="{{ route('admin.p-structure-attr-value.remove-image', $p_structure_attr_value)  }}"
                   class="btn btn-sm btn-danger btn-remove">x</a>
                <img src="{{ $p_structure_attr_value->getImagePath() }}" style="width: 200px;">
            </div>
        @endif
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">alias تصویر</span>
        <input class="form-control input-sm" name="image_alias" value="{{$p_structure_attr_value->image_alias}}">
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
