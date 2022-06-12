@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.directory.index')}}">پوشه ها</a></li>
    <li class="active"><a href="{{route('admin.directory.index')}}">اضافه کردن محصول مرتبط</a></li>

@endsection

@section('form_title')اضافه کردن محصول مرتبط@endsection

@section('form_attributes') action="{{route('admin.directory.do-link-product', $directory)}}" method="POST" @endsection

@section('form_body')

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">نام پوشه‌</span>
            <input class="form-control input-sm" name="directory_title" value="{{ $directory->title }}" disabled>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">شناسه‌ی محصول</span>
            <input class="form-control input-sm" name="product_id" type="number" value="{{ old('product_id') }}">
        </div>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
