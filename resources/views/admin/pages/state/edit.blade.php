@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.state.index')}}">استان ها</a></li>
    <li class="active"><a href="{{route('admin.state.create', compact('state'))}}">ویرایش استان</a></li>

@endsection

@section('form_title')ویرایش استان@endsection

@section('form_attributes') action="{{route('admin.state.update', $state)}}" method="POST" @endsection

@section('form_body')
    {{ method_field('PUT') }}
    <input type="hidden" name="id" value="{{ $state->id }}">
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام استان</span>
        <input class="form-control input-sm" name="name" value="{{ $state->name }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <a class="btn btn-sm btn-primary pull-left" href="{{route('admin.state.show', $state)}}">
            مشاهده شهر ها
        </a>
        <a class="btn btn-sm btn-success pull-left" href="{{route('admin.city.create')}}?state_id={{ $state->id }}">
            افزودن شهر جدید
        </a>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
