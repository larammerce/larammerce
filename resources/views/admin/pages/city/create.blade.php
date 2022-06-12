@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.city.index')}}">شهر ها</a></li>
    <li class="active"><a href="{{route('admin.city.create')}}">اضافه کردن شهر جدید</a></li>

@endsection

@section('form_title')اضافه کردن شهر@endsection

@section('form_attributes') action="{{route('admin.city.store')}}" method="POST" @endsection

@section('form_body')
    <input type="hidden" name="state_id" value="{{$state->id}}">
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام استان</span>
        <input class="form-control input-sm" value="{{$state->name}}" disabled>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام شهر</span>
        <input class="form-control input-sm" name="name" value="{{old('name')}}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">&nbsp منطقه دار؟
            <input id="has_district" name="has_district" type="checkbox" value="1" @if(old('has_district')) checked @endif/>
            <label for="has_district"></label>
        </span>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
