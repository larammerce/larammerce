@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.setting.appliances')}}">تنظیمات</a></li>
    <li><a href="{{route('admin.setting.appliances')}}">مدیریت ماژول ها</a></li>
@endsection

@section('main_content')
    <div class="inner-container">
        <div class="view-port setting-page">
            @foreach(ApplianceService::getSettingAppliances() as $settingAppliance)
                @include("admin.components.appliance-item", ["appliance_item" => $settingAppliance])
            @endforeach
        </div>
    </div>
@endsection
