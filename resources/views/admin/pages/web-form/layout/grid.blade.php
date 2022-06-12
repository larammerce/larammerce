@foreach($webForms as $webForm)
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 grid-item roless">
        <div class="item-container">
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 col">
                <div class="img-container">
                    <img class="img-responsive" src="/admin_dashboard/images/No_image.jpg.png">
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 col">
                <div class="label">شناسه</div>
                <div>{{$webForm->id}}#</div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col">
                <div class="label">شناساگر</div>
                <div>{{$webForm->identifier}}</div>
            </div>
        </div>
    </div>
@endforeach