@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.district.index')}}">منطقه ها</a></li>
    <li class="active"><a href="{{route('admin.district.create')}}">اضافه کردن منطقه جدید</a></li>

@endsection

@section('form_title')اضافه کردن منطقه@endsection

@section('form_attributes') action="{{route('admin.district.store')}}" method="POST" @endsection

@section('form_body')
    <input type="hidden" name="city_id" value="{{$city->id}}">
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام استان</span>
        <input class="form-control input-sm" value="{{$city->state->name}}" disabled>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام شهر</span>
        <input class="form-control input-sm" value="{{$city->name}}" disabled>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام منطقه</span>
        <input class="form-control input-sm" name="name" value="{{old('name')}}">
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
