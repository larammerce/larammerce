@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.directory.index')}}">پوشه ها</a></li>
    <li class="active"><a href="{{route('admin.directory.appliances')}}">اضافه کردن آیتم جدید</a></li>

@endsection

@section('main_content')
    <div class="inner-container">
        <div class="view-port explore-page">
            @foreach(ApplianceService::getExploreAppliances() as $exploreAppliance)
                @if($config[$exploreAppliance->getId()])
                    <div class="appliance-container col-lg-1 col-md-2 col-sm-3 col-xs-6">
                        <a href="{{$exploreAppliance->getUrl()}}@if(isset($directory))?directory_id={{$directory->id}}@endif"
                           class="appliance-content">
                            <div class="h-icon {{$exploreAppliance->getIcon()}} square-ratio"></div>
                            <div class="appliance-detail">
                                <h3 class="appliance-title">{{trans($exploreAppliance->getName())}}</h3>
                            </div>
                        </a>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@stop