@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.customer-address.index')}}">آدرس های مشتریان</a></li>
    <li class="active"><a href="{{route('admin.customer-address.create')}}">اضافه کردن آدرس</a></li>

@endsection

@section('form_title')اضافه کردن آدرس@endsection

@section('form_attributes') action="{{route('admin.customer-address.store')}}" method="POST" @endsection

@section('form_body')
    <input name="customer_user_id" type="hidden" value="{{ $customer_user->id }}">
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام خیردار مربوطه</span>
        <input class="form-control input-sm" name="customer" value="{{ $customer_user->user->full_name }}" disabled>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام آدرس</span>
        <input class="form-control input-sm" name="name" value="{{ old('name') }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام فرد دریافت کننده</span>
        <input class="form-control input-sm" name="transferee_name" value="{{ old('transferee_name') }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">کد پستی</span>
        <input class="form-control input-sm" name="zipcode" type="number" value="{{ old('zipcode') }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">شماره تلفن ثابت</span>
        <input class="form-control input-sm" name="phone_number" type="number" value="{{ old('phone_number') }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">استان</span>
        <select class="form-control input-sm" name="state_id">
            @foreach(\App\Models\State::all() as $state)
                <option value="{{$state->id}}">{{$state->name}}</option>
            @endforeach
        </select>
    </div>
    {{-- TODO: make dynamic select inputs for city and district due to parent selection --}}
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">شهر</span>
        <input class="form-control input-sm" name="city_id">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">منطقه</span>
        <input class="form-control input-sm" name="district_id">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">ادامه توضیحات آدرس</span>
        <textarea class="form-control input-sm" name="superscription">{{ old('superscription') }}</textarea>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
