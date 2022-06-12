@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.shop.appliances')}}">تنظیمات فروشگاه</a></li>
    <li><a href="{{route('admin.shop.appliances')}}">مدیریت ماژول ها</a></li>
@endsection

@section('main_content')
    <div class="inner-container">
        <div class="view-port setting-page">
            @foreach(ApplianceService::getShopAppliances() as $shopAppliance)
                <div class="appliance-container col-lg-1 col-md-2 col-sm-3 col-xs-6">
                    <a href="{{$shopAppliance->getUrl()}}" class="appliance-content">
                        <div class="h-icon {{$shopAppliance->getIcon()}} square-ratio"></div>
                        <div class="appliance-detail">
                            <h3 class="appliance-title">{{trans($shopAppliance->getName())}}</h3>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endsection