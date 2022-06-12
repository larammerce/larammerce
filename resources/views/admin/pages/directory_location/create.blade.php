@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.directory-location.index')}}">مدیریت محدودیت فروش</a></li>
    <li class="active"><a href="{{route('admin.directory-location.create')}}">اضافه کردن ناحیه</a></li>

@endsection

@section('form_title')اضافه کردن ناحیه@endsection

@section('form_attributes') action="{{route('admin.directory-location.store')}}" method="POST" @endsection

@section('form_body')
    <div id="address-module">
        <input type="hidden" name="directory_id" value="{{$directory->id}}">
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <input type="text" class="form-control input-sm" name="state_id"
                   value="{{old('state_id')}}" placeholder="نام استان"
                   id="state-selector"
                   data-url="{{route('api.v1.location.get-states')}}"
                   @if(old('state_id') != null )
                   data-initial-value='{{get_state_json_by_id(old('state_id'))}}'
                @endif >

        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <input type="text" class="form-control input-sm" name="city_id"
                   value="{{old('city_id')}}" placeholder="نام شهر"
                   id="city-selector"
                   data-url-base="{{route('api.v1.location.get-cities')}}"
                   @if(old('city_id') != null )
                   data-initial-value='{{get_city_json_by_id(old('city_id'))}}'
                @endif >

        </div>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
