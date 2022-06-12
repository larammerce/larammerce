@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.web-page.index')}}">صفحات وب</a></li>
    <li class="active"><a href="{{route('admin.web-page.edit', $webPage)}}">ویرایش صفحه وب</a></li>

@endsection

@section('form_title')ویرایش صفحه وب@endsection

@section('form_attributes') action="{{route('admin.web-page.update', $webPage)}}" method="POST" @endsection

@section('form_body')
    {{ method_field('PUT')}}
    <input type="hidden" name="initial" value="1">
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">عنوان</span>
        <input class="form-control input-sm" value="{{$webPage->directory->title}}" disabled>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام بلید</span>
        <select class="form-control" name="blade_name">
            @foreach(get_template_views() as $view)
                <option value="{{$view}}">{{$view}}</option>
            @endforeach
        </select>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
