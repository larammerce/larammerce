@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.color.index')}}">رنگ ها</a></li>
    <li class="active"><a href="{{route('admin.color.edit', $color)}}">ویرایش رنگ</a></li>

@endsection

@section('form_title')ویرایش رنگ@endsection

@section('form_attributes') action="{{route('admin.color.update', $color)}}" method="POST" enctype="multipart/form-data" @endsection

@section('form_body')
    {{ method_field('PUT') }}
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام رنگ</span>
        <input class="form-control input-sm" name="name" value="{{ $color->name }}">
    </div>
    <div class="input-group group-sm col-lg-1 col-sm-2 col-md-2 col-xs-3">
        <span class="label">انتخاب رنگ</span>
        <input class="form-control input-sm" type="color" name="hex_code" value="{{ $color->hex_code }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <label>تصویر</label>
        @if(!$color->hasImage())
            <input class="form-control input-sm" name="image" type="file" multiple="true">
        @else
            <div class="photo-container">
                <a href="{{ route('admin.color.remove-image', $color)  }}"
                   class="btn btn-sm btn-danger btn-remove">x</a>
                <img src="{{ $color->getImagePath() }}" style="width: 200px;">
            </div>
        @endif
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">کپشن</span>
        <input class="form-control input-sm" name="caption" value="{{$color->caption}}">
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
