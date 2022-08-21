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
    <link rel="stylesheet" type="text/css" href="/admin_dashboard/css/app-22-08-09.css"/>
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
        <div class="col-md-3">
            <div class="numeric-report-container">
                <div class="loader-layer"><i class="fa fa-4x fa-refresh fa-spin"></i></div>
                <div class="livenow">
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
                <h1>فروش امروز</h1>
                <p>
                    <span id="report-daily-amount">۱۰۰۰۰۰</span>
                    <span>ریال</span>
                </p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="numeric-report-container">
                <div class="livenow">
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
                <h1>فروش ماه جاری</h1>
                <p>
                    <span id="report-daily-amount">۱۰۰۰۰۰</span>
                    <span>ریال</span>
                </p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="numeric-report-container">
                <div class="livenow">
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
                <h1>فروش سال جاری</h1>
                <p>
                    <span id="report-daily-amount">۱۰۰۰۰۰</span>
                    <span>ریال</span>
                </p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="numeric-report-container">
                <h1>فروش سال گذشته</h1>
                <p>
                    <span id="report-daily-amount">۱۰۰۰۰۰</span>
                    <span>ریال</span>
                </p>
            </div>
        </div>

    </div>
</div>
<div class="container-fluid data-reports">
    <div class="row">
        <div class="col-md-12">
            <div class="report-box">
                <canvas id="overall-bar-chart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="report-box">
                <h1>پر فروش و کم فروش ترین محصولات ماه جاری</h1>
                <div class="data-container">
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-up"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-up"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-up"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-up"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-up"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-up"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-up"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-up"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-up"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>

                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-up"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-down"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="report-box">
                <h1>پر فروش و کم فروش ترین محصولات سال جاری</h1>
                <div class="data-container">
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-up"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-up"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-up"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-up"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-down"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="report-box">
                <h1>پر فروش و کم فروش ترین محصولات سال گذشته</h1>
                <div class="data-container">
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-up"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-up"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-up"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-up"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-down"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="report-box">
                <h1>پر فروش و کم فروش ترین محصولات ماه جاری</h1>
                <div class="data-container">
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-up"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-up"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-up"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-up"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-down"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="report-box">
                <h1>پر فروش و کم فروش ترین محصولات سال جاری</h1>
                <div class="data-container">
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-up"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-up"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-up"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-up"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-count">
                            <span><i class="fa fa-arrow-down"></i></span>
                            <span> #1 </span>
                        </div>
                        <div class="col-md-7 col-title">پر فروش و کم فروش ترین محصولات ماه</div>
                        <div class="col-md-3 col-amount">۳۶۰۰۰۰۰ ریال</div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@include("admin.templates.underscore_needle")
<script data-main="/admin_dashboard/js/all-22-08-09" src="/admin_dashboard/vendor/requirejs/require.js"></script>
</body>
</html>
