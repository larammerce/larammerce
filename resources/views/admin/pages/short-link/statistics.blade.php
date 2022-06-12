@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.short-link.index')}}">لینک های کوتاه</a></li>
    <li class="active"><a href="{{route('admin.short-link.index')}}">لیست لینک ها</a></li>

@endsection

@section('main_content')
    <div class="inner-container row">
        <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
            <script>window.PAGE_ID = "admin.pages.short-link.stats"</script>
            <canvas id="line-chart" width="600" height="350"></canvas>
            <input type="hidden" id="total-count" value="{{$total_count}}">
            <input type="hidden" id="stats-data" value="{{$stats_data}}">
        </div>
        <div class="col-lg-2 col-md-2">
            <div style="margin-top: 50%;margin-right: 30%;margin-bottom: 10%;margin-left: 10%">

                <button type="button" class="btn btn-primary" style="width: 100px" id="weekly-report-btn">هفتگی</button>

            </div>
            <div style="margin-top: 10%;margin-right: 30%;margin-bottom: 10%;margin-left: 10%">

                <button type="button" class="btn btn-primary" style="width: 100px" id="monthly-report-btn">ماهانه
                </button>

            </div>
            <div style="margin-top: 10%;margin-right: 30%;margin-bottom: 10%;margin-left: 10%">

                <button type="button" class="btn btn-primary" style="width: 100px" id="yearly-report-btn">سالانه
                </button>

            </div>
            <div style="margin-top: 10%;margin-right: 30%;margin-bottom: 10%;margin-left: 10%">

                <p style="width: 100px">تعداد کل بازدیدها: {{$total_count}}</p>

            </div>
        </div>

    </div>
@endsection
