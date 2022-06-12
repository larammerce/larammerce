@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.rate.index')}}">نظرات کاربران</a></li>
    <li class="active"><a href="{{route('admin.rate.edit', $rate)}}">ویرایش نظرات کاربران</a></li>
@endsection

@section('form_title')ویرایش نظر کاربر@endsection

@section('form_attributes') action="{{route('admin.rate.update', $rate)}}" method="POST" form-with-hidden-checkboxes @endsection

@section('form_body')
    {{ method_field('PUT') }}
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">امتیاز</span>
        <input class="form-control input-sm" value="{{ $rate->value }}" disabled>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">پیام</span>
        <textarea class="form-control input-sm" name="comment">{{ $rate->comment }}</textarea>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">کاربر</span>
        <input class="form-control input-sm" value="{{$rate->customerUser->user->full_name}}" disabled>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">شماره تماس</span>
        <input class="form-control input-sm" value="{{$rate->customerUser->main_phone}}" disabled>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">تایید نظر؟ &nbsp
            <input id="is-accepted" name="is_accepted" type="checkbox" value="{{true}}"
                   @if($rate->is_accepted) checked @endif/>
            <label for="is-accepted"></label>
            <input id="is-accepted_hidden" name="is_accepted" type="hidden" value="{{false}}"/>
        </span>
        <a class="btn btn-sm btn-primary pull-left"
           href="{{route('public.view-product', $rate->object)}}" target="_blank">
            نمایش محصول
        </a>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
