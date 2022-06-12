@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.discount-group.index')}}">پلن های تخفیف</a></li>
    <li><a href="{{route('admin.discount-group.product-filter.index', $discount_group)}}">لیست فیلترهای محصول</a></li>
    <li class="active"><a href="#">اتصال فیلتر جدید</a></li>

@endsection

@section('form_title')اتصال فیلتر جدید@endsection

@section('form_attributes')
    action="{{route('admin.discount-group.product-filter.attach', $discount_group)}}" method="POST"
@endsection

@section('form_body')
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">فیلتر محصولات</span>
            <select class="form-control input-sm" name="product_filter_id">
                @foreach($product_filters as $product_filter)
                    <option value="{{$product_filter->id}}">{{$product_filter->title}}</option>
                @endforeach
            </select>
        </div>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
