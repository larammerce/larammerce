@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.customer-address.index')}}">آدرس های مشتریان</a></li>
    <li class="active"><a href="{{route('admin.customer-address.edit', $customer_address)}}">ویرایش آدرس</a></li>

@endsection

@section('form_title')ویرایش آدرس@endsection

@section('form_attributes') action="{{route('admin.customer-address.update', $customer_address)}}" method="POST" @endsection

@section('form_body')
    {{ method_field('PUT')}}
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام خیردار مربوطه</span>
        <input class="form-control input-sm" name="customer" value="{{ $customer_address->customer->user->full_name }}"
               disabled>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام آدرس</span>
        <input class="form-control input-sm" name="name" value="{{ $customer_address->name }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام فرد دریافت کننده</span>
        <input class="form-control input-sm" name="transferee_name" value="{{ $customer_address->transferee_name }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">کد پستی</span>
        <input class="form-control input-sm" name="zipcode" type="number" value="{{ $customer_address->zipcode }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">شماره تلفن ثابت</span>
        <input class="form-control input-sm" name="phone_number" type="number"
               value="{{ $customer_address->phone_number }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">استان</span>
        <select class="form-control input-sm" name="state_id">
            @foreach(\App\Models\State::all() as $state)
                <option value="{{$state->id}}" @if($customer_address->state_id == $state->id) selected @endif>
                    {{$state->name}}
                </option>
            @endforeach
        </select>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">شهر</span>
        <select class="form-control input-sm" name="city_id">
            @foreach($customer_address->state->cities as $city)
                <option value="{{$city->id}}" @if($customer_address->city_id == $city->id) selected @endif>
                    {{$city->name}}
                </option>
            @endforeach
        </select>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">منطقه</span>
        <select class="form-control input-sm" name="district_id"
                @if(!$customer_address->city->has_district) disabled @endif>
            @foreach($customer_address->city->districts as $district)
                <option value="{{$district->id}}" @if($customer_address->district_id == $district->id) selected @endif>
                    {{$district->name}}
                </option>
            @endforeach
        </select>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">ادامه توضیحات آدرس</span>
        <textarea class="form-control input-sm" name="superscription">{{ $customer_address->superscription }}</textarea>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
