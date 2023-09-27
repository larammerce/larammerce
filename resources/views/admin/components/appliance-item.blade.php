<div class="appliance-container">
    <a href="{{$appliance_item->getUrl()}}" class="appliance-content">
        <div class="h-icon {{$appliance_item->getIcon()}} appliance-icon"></div>
        <div class="appliance-detail">
            <h3 class="appliance-title">{{trans($appliance_item->getName())}}</h3>
        </div>
    </a>
</div>
