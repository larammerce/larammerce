@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.district.index')}}">منطقه ها</a></li>
    <li class="active"><a href="{{route('admin.district.edit', $district)}}">ویرایش منطقه</a></li>

@endsection

@section('form_title')ویرایش منطقه@endsection

@section('form_attributes') action="{{route('admin.district.update', $district)}}" method="POST" @endsection

@section('form_body')
    {{ method_field('PUT') }}
    <input type="hidden" name="id" value="{{ $district->id }}">
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام استان</span>
        <input class="form-control input-sm" value="{{$district->city->state->name}}" disabled>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام شهر</span>
        <input class="form-control input-sm" value="{{$district->city->name}}" disabled>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام منطقه</span>
        <input class="form-control input-sm" name="name" value="{{$district->name}}">
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
