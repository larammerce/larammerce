@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.product.index')}}">محصولات</a></li>
    <li class="active"><a href="{{route('admin.product.create')}}">اضافه کردن محصول</a></li>

@endsection

@section('form_title')
    اضافه کردن محصول
@endsection

@section('form_attributes')
    action="{{route('admin.product.store')}}" method="POST"
@endsection

@section('form_body')

    <input type="hidden" name="directory_id" @if($directory) value="{{ $directory->id }}" @endif>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام پوشه مربوطه</span>
        <input class="form-control input-sm" name="directory" @if($directory) value="{{ $directory->title }}"
               @endif disabled>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام قالب محصول</span>
        <select class="form-control input-sm" name="p_structure_id">
            @foreach($p_structures as $p_structure)
                <option value="{{ $p_structure->id }}"
                        @if(old('p_structure_id') == $p_structure->id) selected @endif>
                    {{ $p_structure->title }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">عنوان</span>
        <input class="form-control input-sm" name="title" value="{{ old('title') }}" maxlength="62">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">کد کالا</span>
        <input class="form-control input-sm" name="code" value="{{ old('code') }}">
    </div>

    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12"
         style="margin-bottom: 40px;">
        <span class="material-switch pull-right"> آیا قصد ایجاد پکیج محصولات دارید؟ &nbsp
            <input id="is_package" name="is_package" type="checkbox" value="1"
                   @if(old('is_package')) checked @endif/>
            <label for="is_package"></label>
        </span>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
