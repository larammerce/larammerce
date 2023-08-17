@extends('admin.form_layout.col_12')

@section('bread_crumb')
    <li><a href="{{route('admin.invoice.index')}}">فاکتورها</a></li>
    <li class="active"><a href="{{route('admin.invoice.filter')}}">فیلتر کردن فاکتورها</a></li>
@endsection

@section('form_title')انتخاب نوع فیلتر@endsection

@section('form_attributes') action="{{route('admin.invoice.index')}}?filtered=true" method="POST" form-with-hidden-checkboxes @endsection

@section('form_body')
    {{ method_field('GET') }}
    <script>window.PAGE_ID = "admin.pages.invoice.filter";</script>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">نام خریدار</span>
            <input class="form-control input-sm" name="customer_user_fullname" value="">
        </div>
    </div>
@endsection

@section('form_footer')
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="اعمال فیلتر">
@endsection
