<div class="col-md-3">
    <div class="numeric-report-container" id="{{$id}}">
        <div class="loader-layer"><i class="fa fa-4x fa-refresh fa-spin"></i></div>
        @if(($is_live ?? true))
            <div class="livenow">
                <div></div>
                <div></div>
                <div></div>
            </div>
        @endif
        <h1>{{$title}}</h1>
        <p>
            <span class="price-data"></span>
            <span>ریال</span>
        </p>
    </div>
</div>
