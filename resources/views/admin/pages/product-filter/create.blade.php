@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.product-filter.index')}}">فیلتر سفارشی محصولات</a></li>
    <li class="active"><a href="{{route('admin.product-filter.create')}}">اضافه کردن فیلتر</a></li>
@endsection

@section('form_title')اضافه کردن فیلتر@endsection

@section('form_attributes') action="{{route('admin.product-filter.store')}}" method="POST" form-with-hidden-checkboxes @endsection

@section('form_body')

    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">عنوان فیلتر</span>
        <input class="form-control input-sm" name="title" value="{{old('title')}}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">کد شناساگر</span>
        <input class="form-control input-sm" name="identifier" value="{{old('identifier')}}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12 filled">
        <span class="label">جستار مرتبط</span>
        <select class="form-control input-sm" name="product_query_id">
            <option @if(old("product_query_id") == null) selected @endif value disabled>
                بدون جستار
            </option>
            @foreach(App\Models\ProductQuery::all() as $product_query)
                <option value="{{$product_query->id}}"
                        @if(old("product_query_id") == $product_query->id) selected @endif>
                    {{$product_query->title}}
                </option>
            @endforeach
        </select>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
