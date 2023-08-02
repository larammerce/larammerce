@extends('admin.form_layout.col_10')

@section('bread_crumb')
    <li><a href="{{route('admin.product-filter.index')}}">فیلتر سفارشی محصولات</a></li>
    <li class="active"><a href="{{route('admin.product-filter.create')}}">ویرایش فیلتر</a></li>
@endsection

@section('form_title')
    ویرایش فیلتر سفارشی محصولات
@endsection

@section('form_attributes')
    action="{{route('admin.product-filter.update', $product_filter)}}" method="POST" form-with-hidden-checkboxes
@endsection

@section('form_body')
    <script>window.PAGE_ID = "admin.pages.product-filter.edit"</script>
    {{ method_field('PUT') }}
    <input type="hidden" value="{{$product_filter->id}}">
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <div
                    class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12" @roleinput($product_filter,
            "identifier")>
            <span class="label">کد معرف</span>
            <input class="form-control input-sm" value="{{$product_filter->identifier}}" disabled>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
        <div
                class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12" @roleinput($product_filter,
        "title")>
        <span class="label">عنوان گروه محصول</span>
        <input class="form-control input-sm" name="title" value="{{$product_filter->title}}">
    </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
        <div
                class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12" @roleinput($product_filter,
        "title")>
        <span class="label">جستار مرتبط</span>
        <select class="form-control input-sm" name="product_query_id">
            <option @if($product_filter->product_query_id == null) selected @endif value disabled>
                بدون جستار
            </option>
            @foreach(App\Models\ProductQuery::all() as $product_query)
                <option value="{{$product_query->id}}"
                        @if($product_filter->product_query_id == $product_query->id) selected @endif>
                    {{$product_query->title}}
                </option>
            @endforeach
        </select>
    </div>
    </div>
    </div>
    <hr/>
    <div class="row filter-data" @roleinput($product_filter, "filter_data")>
    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" id="directories-section">
        <h4>لیست دایرکتوری‌ها</h4>
        @include("admin.pages.product-filter.directories-section",
            ["directories" => App\Models\Directory::roots()->from(\App\Enums\Directory\DirectoryType::PRODUCT)->get()])
    </div>
    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" id="filters-section">
        <h4>لیست فیلترها</h4>
        @include("admin.pages.product-filter.filters-section",
            ["p_structure_attr_keys" => App\Models\PStructureAttrKey::orderBy("title", "ASC")->get()])
    </div>
    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" id="selected-tags-section">
        <h4>فیلترهای انتخاب شده</h4>
        <div class="row filter-selected-tags-container input-group tag-manager"></div>
    </div>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
