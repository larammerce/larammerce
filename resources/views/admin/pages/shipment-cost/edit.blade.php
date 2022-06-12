@extends('admin.form_layout.col_6')

@section('bread_crumb')
    <li><a href="{{route('admin.setting.appliances')}}">تنظیمات</a></li>
    <li class="active"><a href="{{route('admin.setting.shipment-cost.edit')}}">ویرایش تنظیمات هزینه ارسال</a></li>

@endsection

@section('form_title')ویرایش تنظیمات هزینه ارسال@endsection

@section('form_attributes') action="{{route('admin.setting.shipment-cost.update')}}" method="POST" form-with-hidden-checkboxes @endsection

@section('form_body')
    <script>window.PAGE_ID = "admin.pages.shipment-cost.edit";</script>
    {{ method_field('PUT') }}
    <h4>تنظیمات پیش‌فرض</h4>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="input-group with-icon with-unit group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <i class="fa fa-dollar"></i>
                <span class="label">هزینه ارسال محصولات</span>
                <input class="form-control input-sm" name="shipment_cost" act="price"
                       value="{{old("shipment_cost") ?: $shipment_cost_model->getShipmentCost()}}">
                <span class="unit">ریال</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="input-group with-icon with-unit group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <i class="fa fa-dollar"></i>
                <span class="label">حداقل خرید برای ارسال رایگان</span>
                <input class="form-control input-sm" name="minimum_purchase_free_shipment" act="price"
                       value="{{old("minimum_purchase_free_shipment") ?: $shipment_cost_model->getMinimumPurchaseFreeShipment()}}">
                <span class="unit">ریال</span>
            </div>
        </div>
    </div>
    <hr/>
    <h4>تنظیمات به ازای استان‌ها</h4>
    <div id="custom-config-container" data-rows="{{json_encode($shipment_cost_model->getCustomStates())}}">

    </div>
@endsection


@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
