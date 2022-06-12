@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.system-role.index')}}">نقش ها</a></li>
    <li class="active"><a href="{{route('admin.system-role.edit', $role)}}">ویرایش نقش</a></li>

@endsection

@section('form_title')ویرایش نقش@endsection

@section('form_attributes') action="{{route('admin.system-role.update', $role)}}" method="POST"  @endsection

@section('form_body')
    {{ method_field('PUT') }}
    <input type="hidden" name="id" value="{{ $role->id }}">
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام نقش</span>
        <input class="form-control input-sm" name="name" value="{{ $role->name }}">
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
