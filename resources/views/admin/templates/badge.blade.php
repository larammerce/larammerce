<div class="hcms-badge hcms-badge-1" style="color: {{$badge->color}};">
    @if($badge->show_title)
        <h5>{{$badge->title}}</h5>
    @endif
    @if($badge->hasImage())
        <img src="{{$badge->image_path}} " alt="{{$badge->title}}"/>
    @endif
    @if(is_string($badge->icon) and strlen($badge->icon) > 0)
        <i title="{{$badge->title}}" class="{{$badge->icon}}"></i>
    @endif
</div>
