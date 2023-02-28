<!DOCTYPE html>
<html lang="fa">
<head>
    <title>{{get_identity()["title"]}} | گزارشات زنده</title>
    <meta name="generator" content="larammerce"/>

    <link rel="apple-touch-icon" sizes="180x180" href="/admin_dashboard/{{get_identity()["fav"]}}/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/admin_dashboard/{{get_identity()["fav"]}}/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/admin_dashboard/{{get_identity()["fav"]}}/favicon-16x16.png">
    <link rel="manifest" href="/admin_dashboard/{{get_identity()["fav"]}}/site.webmanifest">
    <link rel="mask-icon" href="/admin_dashboard/{{get_identity()["fav"]}}/safari-pinned-tab.svg" color="#ff2e20">
    <link rel="shortcut icon" href="/admin_dashboard/{{get_identity()["fav"]}}/favicon.ico">
    <meta name="apple-mobile-web-app-title" content="Larammerce">
    <meta name="application-name" content="Larammerce">
    <meta name="msapplication-TileColor" content="#ff2e20">
    <meta name="msapplication-config" content="/admin_dashboard/{{get_identity()["fav"]}}/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="/admin_dashboard/vendor/bootstrap/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="/admin_dashboard/vendor/bootstrap/dist/css/bootstrap-theme.min.css"/>
    <link rel="stylesheet" href="/admin_dashboard/vendor/bootstrap-rtl/dist/css/bootstrap-rtl.min.css"/>
    <link rel="stylesheet" href="/admin_dashboard/vendor/font-awesome/css/font-awesome.min.css"/>
    <link rel="stylesheet" type="text/css" href="/admin_dashboard/css/app-22-11-27.css"/>
</head>
<body class="page-reports">
<header class="bs-docs-nav navbar navbar-static-top" id="top">
    <div class="container-fluid">
        <div class="navbar-header">
            <a href="{{url("/admin")}}" class="navbar-brand">بازگشت به پنل مدیریت</a></div>
    </div>
</header>
<script>window.PAGE_ID = "admin.pages.live-reports.index";</script>
<div class="container numeric-reports">
    <div class="row">
        @include("admin.pages.live-reports._live_numeric_data", ["id" => "daily-sales-amount", "title" => "فروش امروز"])
        @include("admin.pages.live-reports._live_numeric_data", ["id" => "monthly-sales-amount", "title" => "فروش ماه جاری"])
        @include("admin.pages.live-reports._live_numeric_data", ["id" => "yearly-sales-amount", "title" => "فروش سال جاری"])
        @include("admin.pages.live-reports._live_numeric_data", ["id" => "previous-year-sales-amount", "title" => "فروش سال گذشته", "is_live" => false])
    </div>
</div>
<div class="container-fluid data-reports">
    <div class="row">
        <div class="col-md-4">
            @include("admin.pages.live-reports._report_table_box", ["id" => "latest_customers", "title" => "آخرین مشتریان ثبت نام شده", "row_el" =>
            "<div class=\"row\" style=\"display: none;\">
            <div class=\"col-md-1 col-count\">
                <span class=\"numeric-data\"><%- row_id %></span>
            </div>
            <div class=\"col-md-7 col-title\"><%- full_name %> (شناسه: <%- id %>)</div>
            <div class=\"col-md-4 col-amount\"><%- created_at_jalali %></div>
            </div>"])
        </div>
        <div class="col-md-8">
            @include("admin.pages.live-reports._report_table_box", ["id" => "latest_payed_orders", "title" => "آخرین سفارشات پرداخت شده", "row_el" =>
            "<div class=\"row\" style=\"display: none;\">
            <div class=\"col-md-1 col-count\">
                <span class=\"numeric-data\"><%- row_id %></span>
            </div>
            <div class=\"col-md-4 col-title\"><%- customer.user.full_name %> (شناسه مشتری: <%- customer.user.id %>)</div>
            <div class=\"col-md-2 \">تاریخ ثبت نام مشتری: <%- customer.user.created_at_jalali %></div>
            <div class=\"col-md-2 \">ثبت سفارش: <%- created_at_jalali %></div>
            <div class=\"col-md-2 col-amount\"><span class=\"price-data\"><%- sum %></span> ریال</div>
            <div class=\"col-md-1 col-amount\"><a href=\"/admin/invoice/<%- id %>\" class=\"btn btn-sm btn-success\" target=\"_blank\"><i class=\"fa fa-eye\"></i></a></div>
            </div>"])
        </div>
    </div>
    @include("admin.pages.live-reports._overall_bar_chart", ["id" => "overall-bar-chart"])
    @include("admin.pages.live-reports._overall_bar_chart", ["id" => "overall-sales-bar-chart"])
    @include("admin.pages.live-reports._overall_bar_chart", ["id" => "categories-availability"])
    @include("admin.pages.live-reports._overall_bar_chart", ["id" => "overall-created-products-per-category"])
    <div class="row">
        <div class="col-md-4">
            @include("admin.pages.live-reports._report_table_box", ["id" => "monthly-categories-table", "title" => "پر فروش و کم فروش ترین دسته بندی‌های ماه جاری", "is_live" => false, "with_cart" => true])
        </div>
        <div class="col-md-4">
            @include("admin.pages.live-reports._report_table_box", ["id" => "yearly-categories-table", "title" => "پر فروش و کم فروش ترین دسته بندی‌های سال جاری", "is_live" => false, "with_cart" => true])
        </div>
        <div class="col-md-4">
            @include("admin.pages.live-reports._report_table_box", ["id" => "previous-year-categories-table", "title" => "پر فروش و کم فروش ترین دسته‌بندی‌های سال گذشته", "is_live" => false, "with_cart" => true])
        </div>
    </div>
    <div class="row" style="margin-bottom: 20px;"></div>
</div>

@include("admin.templates.underscore_needle")
<script data-main="/admin_dashboard/js/all-22-11-27" src="/admin_dashboard/vendor/requirejs/require.js"></script>
</body>
</html>
