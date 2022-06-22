@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.invoice-row.index')}}">محصولات صورت حساب</a></li>
    <li class="active"><a href="{{route('admin.invoice-row.create')}}">اضافه کردن محصول به صورت حساب</a></li>

@endsection

@section('form_title')
    اضافه کردن محصول به صورت حساب
@endsection

@section('form_attributes')
    action="{{route('admin.invoice-row.store')}}" method="POST"
@endsection

@section('form_body')
    <input name="invoice_id" type="hidden" value="{{ $invoice->id }}">
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">محصول</span>
        <select class="form-control input-sm" name="product_id">
            @foreach(\App\Models\PStructure::all() as $productStructure)
                <optgroup label="{{ $productStructure->title }}">
                    @foreach($productStructure->products as $product)
                        <option value="{{$product->id}}">{{ $product->title }}</option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">تعداد</span>
        <input class="form-control input-sm" type="count" name="count" value="{{ old('count') }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">درصد تخفیف</span>
        <input class="form-control input-sm" type="number" name="discount_percentage"
               value="{{ old('discount_percentage') }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">توضیحات</span>
        <textarea class="form-control input-sm" name="description" value="{{ old('description') }}"></textarea>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
