<div class="report-box" id="{{$id}}">
    <h1>{{$title}}</h1>
    <div class="hidden row-template">
        {!! $row_el ??
        "<div class=\"row\">
            <div class=\"col-md-2 col-count\">
                <span><%- id %></span>
            </div>
            <div class=\"col-md-7 col-title\"><%- title %></div>
            <div class=\"col-md-3 col-amount\"><%- total_amount %></div>
        </div>"
         !!}
    </div>
    <div class="loader-layer"><i class="fa fa-4x fa-refresh fa-spin"></i></div>
    <div class="data-container">
    </div>
</div>
