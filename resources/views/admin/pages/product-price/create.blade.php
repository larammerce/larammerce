@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.product-price.index')}}">قیمت محصولات</a></li>
    <li class="active"><a href="{{route('admin.product-price.create')}}">اضافه کردن قیمت جدید</a></li>

@endsection

@section('form_title')اضافه کردن قیمت@endsection

@section('form_attributes') action="{{route('admin.product-price.store')}}" method="POST" @endsection

@section('form_body')
    <input class="form-control input-sm" type="hidden" name="product_id" value="{{$product->id}}">
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">عنوان محصول</span>
        <input class="form-control input-sm" type="text" value="{{$product->title}}" disabled>
    </div>
    <div class="input-group with-unit group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">قیمت جدید</span>
        <input class="form-control input-sm" name="value" value="{{old('value')}}" act="price">
        <span class="unit">تومان</span>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
