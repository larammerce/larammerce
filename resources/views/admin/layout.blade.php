<!DOCTYPE html>
<html lang="fa">
<head>
    <title>{{get_identity()["title"]}}</title>
    <meta name="generator" content="larammerce"/>
    @yield('extra_style')
    <link rel="stylesheet" href="/admin_dashboard/vendor/bootstrap/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="/admin_dashboard/vendor/font-awesome/css/font-awesome.min.css"/>
    <link rel="stylesheet" href="/admin_dashboard/vendor/codemirror/lib/codemirror.css"/>
    <link rel="stylesheet" href="/admin_dashboard/vendor/codemirror/theme/material-palenight.css"/>
    <link rel="stylesheet" href="/admin_dashboard/vendor/jquery-ui/jquery-ui.css">
    <link rel="stylesheet" href="/admin_dashboard/vendor/persianDatepicker/css/persian-datepicker.min.css">
    <link rel="stylesheet" href="/admin_dashboard/vendor/jquery-toast/jquery.toast.min.css">
    <link rel="stylesheet" type="text/css" href="/admin_dashboard/css/app-23-07-18r2.css"/>

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

    {{-- security section for file manager [setting the access key] --}}
    <script>
        window.filemanagerAccessKey = '61a6644cc3f50bdd5d02c9f3bd7c5820c46849059b684a17fa85922752b91be3';
        window.csrf_token = "{{csrf_token()}}";
        window.site_url = "{{env("APP_URL")}}";
    </script>
</head>
<body>
<div class="header-container">
    <nav class="main-nav">
        <div class="right-tools col-md-5 col-sm-4 col-xs-7">
            <div class="profile hidden-xs hidden-md">
                <div class="user-photo">
                    <img class="img-responsive"
                         src="{{\App\Models\User::getEloquentObject(Auth::user())?->systemUser?->getImagePath() ?? "/admin_dashboard/images/No_image.jpg.png"}}"/>
                </div>
                <h5 class="name hidden-sm hidden-xs">{{Auth::user()->name.' '.Auth::user()->family}}</h5>
            </div>
            <div class="action-container">

                <div class="action-button"><a href="{{route("admin.web-form-message.index")}}">
                        <i class="fa fa-bell @if(getUnreadMessage()->count() > 0) shake @endif"></i></a>
                    @if(getUnreadMessage()->count() > 0)
                        <span class="badge badge-message"> {{getUnreadMessage()->count()}} </span>
                    @endif
                </div>
                <div class="action-button"><a data-method="POST" confirm class="virt-form"
                                              data-action="{{url('/logout')}}"><i class="fa fa-sign-out"></i></a></div>
                <div class="action-button">
                    @if(isset(request()->related_model) || isset($related_model))
                        <a href="" type="button" data-toggle="modal" data-target="#classic-search-modal">
                            <i class="fa fa-search"></i>
                            <span class="hidden-xs hidden-sm hidden-md">
                                    جستجوی پیشرفته {{trans("structures.classes.".get_model_entity_name(request()->related_model??$related_model))}}
                                </span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="search-bar col-md-5 col-sm-4 hidden-xs">
        </div>
        <div class="left-tools col-md-2 col-xs-5 col-sm-4">
            <div class="logo-container hidden-md hidden-sm hidden-xs">
                <img src="{{get_identity()["logo"]}}.png" class="header-logo img-responsive"/>
            </div>
            <div class="date-container">{{ TimeService::getCurrentDate() }}</div>
        </div>
    </nav>
    <nav class="apps-nav-container">
        <ul class="apps-nav">
            @foreach(ApplianceService::getToolbarAppliances() as $appliance)
                <li class="app-container" appliance-id="{{$appliance->getId()}}">
                    <a href="{{$appliance->getUrl()}}" class="has-notif">
                        {{--                        <span class="notification-container">0</span>--}}
                        <div class="icon-container">
                            <div class="icon-background">
                                <img class="img-responsive" src="{{$appliance->getIcon()}}"/>
                            </div>
                        </div>
                        <p class="app-name">{{trans($appliance->getName())}}</p>
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</div>
<div class="main-content">
    <div class="file-context-menu">
        <ul>
            <li>
                <button class="btn btn-block btn-sm btn-default" act="open-file">
                    ورود<i class="fa fa-arrow-left"></i>
                </button>
            </li>
            <hr/>
            <li>
                <button class="btn btn-block btn-sm btn-default" act="edit-file">
                    ویرایش<i class="fa fa-edit"></i>
                </button>
            </li>
            <li>
                <button class="btn btn-block btn-sm btn-default" act="show-file-info">
                    نمایش مشخصات<i class="fa fa-info"></i>
                </button>
            </li>
            <hr/>
            <li>
                <button class="btn btn-block btn-sm btn-default disabled" act="copy-file">
                    کپی<i class="fa fa-copy"></i>
                </button>
            </li>
            <li>
                <button class="btn btn-block btn-sm btn-default disabled" act="cut-file">
                    کات<i class="fa fa-cut"></i>
                </button>
            </li>
            <hr/>
            <li>
                <button class="btn btn-block btn-sm btn-default" act="show-file">
                    نمایش محتوا در وبسایت
                </button>
            </li>
        </ul>
    </div>
    <div class="explore-map col-md-2 hidden-sm hidden-xs">
        @include('admin.templates.explore.map')
    </div>
    <div class="explore-view-container col-md-10 col-sm-12 col-xs-12">
        @yield('main_content')
        <div class="bread-crumb">
            <ul>
                @yield('bread_crumb')
            </ul>
        </div>
    </div>
</div>

<div class="hidden-content">
    <div class="error-messages">
        @foreach($errors->getMessages() as $inputName => $messages)
            <ul input-name="{{$inputName}}">
                @foreach($messages as $message)
                    <li>{{$message}}</li>
                @endforeach
            </ul>
        @endforeach
    </div>
    <div class="system-messages">
        <ul>
            @foreach(get_system_messages() as $message)
                <li data-color="{{ $message->color_code }}" data-type="{{ $message->type }}">{!! $message->text !!}</li>
            @endforeach
        </ul>
    </div>
</div>

<script data-main="/admin_dashboard/js/all-23-07-18r2" src="/admin_dashboard/vendor/requirejs/require.js"></script>

@yield('extra_javascript')

@include('admin.templates.underscore_needle')
@include('admin.templates.modals.confirm_modal')
@if(isset(request()->related_model) || isset($related_model))
    @include('admin.templates.modals.classic_search_modal')
    @include('admin.templates.modals.excel_export_modal')
@endif
@yield('outer_content')


</body>
</html>
