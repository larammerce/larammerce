@extends('admin.form_layout.col_8')

@section('bread_crumb')
    <li><a href="{{route('admin.product.index')}}">محصولات</a></li>
    <li class="active"><a href="{{route('admin.product.edit', $product)}}">ویرایش محصول</a></li>

@endsection

@section('form_title')اضافه کردن محصول به پکیج@endsection

@section('form_attributes')  action="{{route('admin.product-package.update', $product)}}" method="POST" @endsection

@section('form_body')
    <script>window.PAGE_ID = "admin.pages.product-package.edit";</script>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <input name="id" type="hidden" value="{{ $product->id }}">
        {{ method_field('PUT') }}
        <div id="product-package-container"
             data-rows="{{json_encode($product->productPackage->getPackageItems())}}">
        </div>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-success">تایید</button>
    <button act="link" class="btn btn-default btn-danger" href="{{route('admin.product.edit', $product)}}">
        بازگشت
    </button>
@endsection