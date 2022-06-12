@extends('admin.form_layout.col_10')

@section('bread_crumb')
    <li><a href="{{route('admin.product-query.index')}}">جستار سفارشی محصولات</a></li>
    <li class="active"><a href="{{route('admin.product-query.create')}}">ویرایش جستار</a></li>
@endsection

@section('form_title')ویرایش گروه محصول@endsection

@section('form_attributes') action="{{route('admin.product-query.update', $product_query)}}" method="POST" form-with-hidden-checkboxes @endsection

@section('form_body')
    {{ method_field('PUT') }}
    <input type="hidden" value="{{$product_query->id}}">
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div
            class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12" @roleinput($product_query, "identifier")>
            <span class="label">کد شناساگر</span>
            <input class="form-control input-sm" value="{{$product_query->identifier}}" disabled>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12" @roleinput($product_query, "title")>
            <span class="label">عنوان جستار</span>
            <input class="form-control input-sm" name="title" value="{{$product_query->title}}">
        </div>
        <div
            class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12" @roleinput($product_query, "take_count")>
            <span class="label">تعداد انتخاب</span>
            <input class="form-control input-sm" name="take_count" value="{{$product_query->take_count}}">
        </div>
        <div
            class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12" @roleinput($product_query, "skip_count")>
            <span class="label">تعداد رد کردن</span>
            <input class="form-control input-sm" name="skip_count" value="{{$product_query->skip_count}}">
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 query-data" @roleinput($product_query, "query_data")>
            <ul class="hidden scope-types">
                <li data-id="sort" data-title="مرتب سازی" data-has-value="false">
                    <ul class="options">
                        <li data-id="asc" data-title="صعودی"></li>
                        <li data-id="desc" data-title="نزولی"></li>
                    </ul>
                </li>
                <li data-id="condition" data-title="شرط" data-has-value="true">
                    <ul class="options">
                        <li data-id="eq" data-title="برابر با" data-comment="مقدار مورد نظر خود را وارد کنید."></li>
                        <li data-id="gt" data-title="بزرگتر از" data-comment="مقدار مورد نظر خود را وارد کنید."></li>
                        <li data-id="lt" data-title="کوچکتر از" data-comment="مقدار مورد نظر خود را وارد کنید."></li>
                        <li data-id="in" data-title="موجود در لیست"
                            data-comment="آیتم های لیست را با ویرگول انگلیسی ',' از هم جدا کنید."></li>
                    </ul>
                </li>
            </ul>
            <ul class="hidden scope-fields">
                <li data-id="id" data-title="شناسه"></li>
                <li data-id="latest_price" data-title="آخرین قیمت"></li>
                <li data-id="code" data-title="کد محصول"></li>
                <li data-id="directory_id" data-title="شماره پوشه"></li>
                <li data-id="created_at" data-title="تاریخ ساخت"></li>
                <li data-id="updated_at" data-title="تاریخ تغییرات"></li>
                <li data-id="average_rating" data-title="میانگین امتیاز"></li>
                <li data-id="count" data-title="موجودی"></li>
                <li data-id="has_discount" data-title="تخفیف دارد"></li>
                <li data-id="is_important" data-title="محصول مهم"></li>
                <li data-id="priority" data-title="اولویت"></li>
            </ul>
            <ul class="hidden initial-data">
                @foreach($product_query->getData() as $index => $scope)
                    <li data-type="{{$scope->type}}"
                        data-field="{{$scope->field}}"
                        data-option="{{$scope->option}}"
                        @if(isset($scope->value)) data-value="{{$scope->value}}" @endif ></li>
                @endforeach
            </ul>
            <h1 class="title">تنظیمات</h1>
            <div class="query-data-container">
            </div>
        </div>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
