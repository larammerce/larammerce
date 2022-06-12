@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.discount-group.index')}}">پلن های تخفیف</a></li>
    <li class="active"><a href="{{route('admin.discount-group.create')}}">اضافه کردن پلن تخفیف</a></li>
@endsection

@section('form_title')اضافه کردن پلن تخفیف@endsection

@section('form_attributes') action="{{route('admin.discount-group.store')}}" method="POST" form-with-hidden-checkboxes @endsection

@section('form_body')
    <script>window.PAGE_ID = "admin.pages.discount-group.create";</script>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">عنوان پلن تخفیف</span>
            <input class="form-control input-sm" name="title" value="{{old('title')}}">
        </div>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection