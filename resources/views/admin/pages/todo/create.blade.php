@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.todo.index')}}">وظایف</a></li>
    <li class="active"><a href="{{route('admin.todo.create')}}">اضافه کردن وظیفه</a></li>

@endsection

@section('form_title')اضافه کردن وظیفه@endsection

@section('form_attributes') action="{{route('admin.todo.store')}}" method="POST"  @endsection

@section('form_body')
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">موضوع</span>
        <input class="form-control input-sm" name="subject" value="{{old('subject')}}">
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
