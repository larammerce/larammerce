@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.shop.appliances')}}">تنظیمات فروشگاه</a></li>
    <li><a href="{{route('admin.shop.appliances')}}">مدیریت ماژول ها</a></li>
@endsection

@section('main_content')
    <div class="inner-container">
        <div class="view-port setting-page">
            @foreach(ApplianceService::getAnalyticAppliances() as $analyticAppliance)
                @include("admin.components.appliance-item", ["appliance_item" => $analyticAppliance])
            @endforeach
        </div>
    </div>
@endsection
