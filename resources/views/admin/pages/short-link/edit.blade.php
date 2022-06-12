@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.short-link.index')}}">لینک های کوتاه</a></li>
    <li class="active"><a href="{{route('admin.short-link.create')}}">ویرایش لینک کوتاه</a></li>
@endsection

@section('form_title')ویرایش لیک کوتاه@endsection

@section('form_attributes') action="{{route('admin.short-link.update', $short_link)}}" method="POST" form-with-hidden-checkboxes @endsection

@section('form_body')
    {{ method_field('PUT') }}
    <script>window.PAGE_ID = "admin.pages.short-link"</script>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">لینک اصلی</span>
        <input class="form-control input-sm" name="link" value="{{ $short_link->link }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12" id="shortened-link-input-div">
        <span class="label">لینک کوتاه</span>
        <input class="form-control input-sm" name="shortened_link" id="shortened-link-input"
               value="{{ $short_link->shortened_link }}">
    </div>
    <button type="button" class="btn btn-default btn-md generate-short-link" id="generate-short-link">تولید لینک
    </button>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
