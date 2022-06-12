@extends('admin.form_layout.col_10')

@section('bread_crumb')
    <li><a href="{{route('admin.setting.appliances')}}">تنظیمات</a></li>
    <li class="active"><a href="{{route('admin.setting.logistic.edit')}}">ویرایش تنظیمات لوجستیک</a></li>

@endsection

@section('form_title')ویرایش تنظیمات لوجستیک@endsection

@section('form_attributes') id="update-form" action="{{route('admin.setting.logistic.update')}}" method="POST" form-with-hidden-checkboxes @endsection

@section('form_body')
    <script>window.PAGE_ID = "admin.pages.logistic.edit";</script>

    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #f33221;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: #1fb857;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        .disabled-cell {
            border: none;
            background-color: #f5c4b8;
        }

        .enabled-cell {
            border: none;
            background-color: #ebf8ee;
        }

        .new-cell {
            border: none;
            background-color: #727272;
        }

    </style>
    {{ method_field('PUT') }}
    <h4>تنظیمات پیش‌فرض</h4>
    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">حد اکثر تعداد کالا (در هر خانه)</span>
                <input class="form-control input-sm" type="text" name="max_items_count"
                       value="{{ $max_items_count }}" act="price">
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">حداکثر مجموع قیمت (در هر خانه)</span>
                <input class="form-control input-sm" type="text" name="max_total_price"
                       value="{{ $max_total_price }}" act="price">
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">تعداد روزهای غیر فعال (از امروز)</span>
                <input class="form-control input-sm" type="text" name="rows_offset"
                       value="{{ $rows_offset }}" act="price">
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">تعداد روزهای قابل رویت (از امروز)</span>
                <input class="form-control input-sm" type="text" id="rows-available" name="rows_available"
                       value="{{ $rows_available }}" act="price">
            </div>
        </div>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th scope="col" style="text-align: right" id="logistic-table-column-head">#</th>
            @foreach($hours as $hour)
                <th scope="col" style="text-align: right" id="logistic-table-column-head-{{$hour['order']}}">
                    <p id="logistic-table-column-head-text-{{$hour['order']}}" act="persian-number"
                       style="float: right;">{{ $hour["start_hour"].'-'.$hour["finish_hour"] }}</p>
                    <a class="btn btn-danger btn-sm remove-col-btn" href="#"
                       style="border-radius: 50%;float: left;margin-left: 5%;"
                       id="remove-column-btn-{{$hour['order']}}">
                        <i class="fa fa-remove"></i>
                    </a>
                </th>
            @endforeach
            <th scope="col" style="text-align: right"><a class="btn btn-success" href=""
                                                         style="border-radius: 50%;float: left" data-toggle="modal"
                                                         data-target="#myModal">
                    <i class="fa fa-plus"></i>
                </a></th>
        </tr>
        </thead>
        <tbody id="logistic-table-body">
        @foreach($days as $day)

            <tr id="logistic-table-row-{{$day['order']}}">
                <th scope="row" style="text-align: right;border:none"
                    id="logistic-table-row-head-{{ $day['order'] }}">{{ $day['jalali_date'] }}</th>
            @for($i=0;$i<$columns_count;$i++)

                <!--td style="text-align: right;border:none;background-color: #c3f3d2;"-->
                    <td id="logistic-table-cell-{{ $day['order'].'-'.$i }}"
                        class="{{ $cells[$day['order']][$i]['is_enabled']?'enabled-cell':'disabled-cell' }}">
                        <div style="float: right;margin-right: 5%;margin-left: 5%">
                            <p style="float: left" id="total-price-{{ $day['order'].'-'.$i }}"
                               act="persian-number">{{ $cells[$day['order']][$i]["total_price"] }}</p>
                            <br>
                            <p style="float: left">{{ 'مجموع قیمت' }}</p>

                        </div>
                        <div style="float: right;margin-right: 10%;margin-left: 10%">
                            <p style="float: left" id="items-count-{{ $day['order'].'-'.$i }}"
                               act="persian-number">{{ $cells[$day['order']][$i]["items_count"] }}</p>
                            <br>
                            <p style="float: left">{{ 'تعداد کالا' }}</p>

                        </div>


                        <div style="float: left;margin-left: 5%;">
                            <label class="switch">
                                <input id="checkbox-{{ $day['order'].'-'.$i }}" value="{{ $day['order'].'-'.$i }}"
                                       class="checkbox"
                                       type="checkbox" {{ $cells[$day['order']][$i]['is_enabled']?'checked':'' }}>
                                <span class="slider round"></span>
                            </label>
                        </div>

                    </td>

                @endfor

            </tr>
        @endforeach
        <tr>
            <!--th scope="row" style="text-align: right;border: none"><a class="btn btn-success" href="" style="border-radius: 50%">
                    <i class="fa fa-plus"></i>
                </a></th-->

        </tr>
        </tbody>
    </table>

    <input type="hidden" id="logistic-table-cells" value="{{ json_encode($cells) }}">
    <input type="hidden" id="logistic-table-row-heads" value="{{ json_encode($days) }}">
    <input type="hidden" id="logistic-table-head" value="{{ json_encode($hours) }}">

    <input type="hidden" id="max-items-count-value" value="{{ $max_items_count }}">
    <input type="hidden" id="max-total-price-value" value="{{ $max_total_price }}">

    <input type="hidden" id="cells" name="cells" value="{{ json_encode($cells) }}">
    <input type="hidden" id="days" name="days" value="{{ json_encode($days) }}">
    <input type="hidden" id="hours" name="hours" value="{{ json_encode($hours) }}">
    <input type="hidden" id="offset-value" name="old_rows_offset" value="{{ $rows_offset }}">



@endsection


@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">تایید</button>
    <a type="button" href="logistic/get-invoices" class="btn btn-info btn-sm">گزارش گیری</a>
@endsection

@section('outer_content')
    @include('admin.pages.logistic.add-column-modal')
@endsection
