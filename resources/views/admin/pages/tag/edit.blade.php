@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.tag.index')}}">تگ ها</a></li>
    <li class="active"><a href="{{route('admin.tag.create', compact('tag'))}}">ویرایش تگ</a></li>

@endsection

@section('form_title')ویرایش تگ@endsection

@section('form_attributes') action="{{route('admin.tag.update', $tag)}}" method="POST" @endsection

@section('form_body')
    {{ method_field('PUT') }}
    <input type="hidden" name="id" value="{{ $tag->id }}">
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام نقش</span>
        <input class="form-control input-sm" name="name" value="{{ $tag->name }}">
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
