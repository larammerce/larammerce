@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.city.index')}}">شهر ها</a></li>
    <li class="active"><a href="{{route('admin.city.edit', $city)}}">ویرایش شهر</a></li>

@endsection

@section('form_title')ویرایش شهر@endsection

@section('form_attributes') action="{{route('admin.city.update', $city)}}" method="POST" form-with-hidden-checkboxes @endsection

@section('form_body')
    {{ method_field('PUT') }}
    <input type="hidden" name="id" value="{{ $city->id }}">
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام استان</span>
        <input class="form-control input-sm" value="{{$city->state->name}}" disabled>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام شهر</span>
        <input class="form-control input-sm" name="name" value="{{$city->name}}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right"> منطقه دار &nbsp
            <input id="has_district" name="has_district" type="checkbox" value="1" @if($city->has_district) checked @endif/>
            <label for="has_district"></label>
            <input id="has_district_hidden" name="has_district" type="hidden" value="0"/>
        </span>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
