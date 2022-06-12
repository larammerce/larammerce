@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.product-price.index')}}">قیمت محصولات</a></li>
    <li class="active"><a href="{{route('admin.product-price.edit', $product_price)}}">ویرایش قیمت</a></li>

@endsection

@section('form_title')ویرایش قیمت@endsection

@section('form_attributes') action="{{route('admin.product-price.update', $product_price)}}" method="POST" @endsection

@section('form_body')
    {{ method_field('PUT') }}
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">عنوان محصول</span>
        <input class="form-control input-sm" type="text" value="{{$product_price->product->title}}" disabled>
    </div>
    <div class="input-group with-unit group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">قیمت</span>
        <input class="form-control input-sm" name="value" value="{{$product_price->value}}" act="price">
        <span class="unit">تومان</span>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
