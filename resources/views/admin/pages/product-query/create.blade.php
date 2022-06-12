@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.product-query.index')}}">جستار سفارشی محصولات</a></li>
    <li class="active"><a href="{{route('admin.product-query.create')}}">اضافه کردن جستار</a></li>
@endsection

@section('form_title')اضافه کردن جستار@endsection

@section('form_attributes') action="{{route('admin.product-query.store')}}" method="POST" form-with-hidden-checkboxes @endsection

@section('form_body')

    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">عنوان جستار</span>
        <input class="form-control input-sm" name="title" value="{{old('title')}}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">کد شناساگر</span>
        <input class="form-control input-sm" name="identifier" value="{{old('identifier')}}">
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
