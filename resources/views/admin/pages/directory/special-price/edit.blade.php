@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.directory.index')}}">پوشه ها</a></li>
    <li><a href="{{route('admin.directory.edit', $directory)}}">{{$directory->title}}</a></li>
    <li class="active"><a href="#">فروش ویژه گروهی کالاها</a></li>

@endsection

@section('form_title')فروش ویژه گروهی کالاها@endsection

@section('form_attributes')
    action="{{route('admin.directory.special-price.update', $directory)}}" method="POST"
@endsection

@section('form_body')
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <p>
            در صورتی که تمایل دارید برای تمامی محصولاتی که در این شاخه قرار گرفته اند فروش ویژه گروهی تعریف کنید، مقدار
            کاهش مبلغ فروش محصول را در قسمت زیر به درصد وارد کنید:
        </p>
        <hr/>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">درصد کاهش قیمت</span>
            <input class="form-control input-sm" name="descent_percentage" value="{{ old("descent_percentage") }}"
                   act="price">
        </div>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
    <a class="btn btn-danger btn-sm virt-form"
       data-action="{{ route('admin.directory.special-price.destroy', $directory) }}"
       data-method="DELETE" confirm>حذف فروش ویژه این گروه</a>
@endsection
