@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.p-structure-attr-key.index')}}">ويژگی های محصولات</a></li>
    <li class="active"><a href="{{route('admin.p-structure-attr-key.index')}}">اضافه کردن ويژگی</a></li>

@endsection

@section('form_title')اضافه کردن ويژگی@endsection

@section('form_attributes') action="{{route('admin.p-structure-attr-key.store')}}" method="POST" form-with-hidden-checkboxes @endsection

@section('form_body')
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">عنوان ویژگی</span>
        <input class="form-control input-sm" name="title" value="{{old('title')}}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">سطح اولویت</span>
        <input class="form-control input-sm" name="priority" value="{{old('priority')}}">
    </div>
    <div class="input-group filled group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نوع نمایش</span>
        <select name="show_type" class="form-control input-sm">
            @foreach(PSAttrKeyShowType::toMap() as $key => $value )
                <option value="{{$value}}">
                    {{trans('general.ps_attr_key_show_type.'.$value)}}
                </option>
            @endforeach
        </select>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
