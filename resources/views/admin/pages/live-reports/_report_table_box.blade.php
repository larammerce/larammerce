<div class="report-box" id="{{$id}}">
    @if(($is_live ?? true))
        <div class="livenow">
            <div></div>
            <div></div>
            <div></div>
        </div>
    @endif
    <h1>{{$title}}</h1>
    <script type="text/template" class="hidden row-template">
        {!! $row_el ??
        "<div class=\"row\" style=\"display: none;\">
            <div class=\"col-md-1 col-count\">
                <span class=\"numeric-data\"><%- row_id %></span>
            </div>
            <div class=\"col-md-7 col-title\"><%- title %> (شناسه: <%- id %>)</div>
            <div class=\"col-md-4 col-amount\"><span class=\"price-data\"><%- total_amount %></span> ریال</div>
        </div>"
         !!}
    </script>
    <div class="loader-layer"><i class="fa fa-4x fa-refresh fa-spin"></i></div>
    <div class="data-container">
    </div>
</div>
