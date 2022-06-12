@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.shop.appliances')}}">تنظیمات فروشگاه</a></li>
    <li><a href="{{route('admin.shop.appliances')}}">مدیریت ماژول ها</a></li>
@endsection

@section('main_content')
    <div class="inner-container">
        <div class="view-port setting-page">
            @foreach(ApplianceService::getAnalyticAppliances() as $analyticAppliance)
                <div class="appliance-container col-lg-1 col-md-2 col-sm-3 col-xs-6">
                    <a href="{{$analyticAppliance->getUrl()}}" class="appliance-content">
                        <div class="h-icon {{$analyticAppliance->getIcon()}} square-ratio"></div>
                        <div class="appliance-detail">
                            <h3 class="appliance-title">{{trans($analyticAppliance->getName())}}</h3>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endsection