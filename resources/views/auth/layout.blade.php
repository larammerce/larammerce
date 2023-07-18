<!DOCTYPE html>
<html lang="fa">
<head>
    <title>{{get_identity()["title"]}}</title>
    <meta name="generator" content="larammerce"/>
    @yield('extra_style')

    <link rel="stylesheet" href="/admin_dashboard/vendor/bootstrap/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="/admin_dashboard/vendor/font-awesome/css/font-awesome.min.css"/>
    <link rel="stylesheet" href="/admin_dashboard/vendor/codeBase/codebase.min.css"/>
    <link rel="stylesheet" href="/admin_dashboard/vendor/font-awesome/css/font-awesome-rtl.css"/>
    <link rel="stylesheet" href="/admin_dashboard/vendor/nanoscroller-rtl.css"/>
    <link rel="stylesheet" href="/admin_dashboard/vendor/switchery-rtl.css"/>
    <link rel="stylesheet" href="/admin_dashboard/vendor/bootstrap-switch-rtl.css"/>
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

    <style>
        .login-page form .btn:hover {
            border: 1px solid{{get_identity()["color"]}};
            color: {{get_identity()["color"]}};
        }

        .login-page form .btn {
            background: {{get_identity()["color"]}};
        }

        .login-page .page-title:after {
            border-top: 2px solid{{get_identity()["color"]}};
        }

        .login-page .website-link {
            color: {{get_identity()["color"]}};
        }
    </style>
</head>
<body class="login-page">


<div id="page-container" class="main-content-boxed side-trans-enabled">
    <main id="main-container" style="min-height: 450px;">
        <div class="bg-image" style="background-image: url('/admin_dashboard/images/bg-background.jpg');">
            <div class="row mx-0 bg-black-op">
                <div class="hero-static col-md-6 col-xl-8 d-none d-md-flex align-items-md-end">
                    <div class="p-30 js-appear-enabled animated fadeIn" data-toggle="appear">
                        <p class="font-size-h3 font-w600 text-white">
                            {{get_identity()["name"]}}
                        </p>
                    </div>
                </div>
                <div
                    class="hero-static rtl-menu-login col-md-6 col-xl-4 d-flex align-items-center js-appear-enabled animated fadeInRight"
                    data-toggle="appear" data-class="animated fadeInRight">
                    <div class="content content-full">
                        <a class="link-effect" href="{{get_identity()["url"]}}">
                            <div class="img-logo">
                                <img alt="Logo" src="{{get_identity()["logo"]}}.svg">
                            </div>
                        </a>
                        <h1 class="page-title">{{get_identity()["motto"]}}</h1>
                        <form class="js-validation-signin form-horizontal ls_form" @yield('form_attributes')>
                            {!! csrf_field() !!}
                            @yield('form_body')
                        </form>
                        <a href="{{get_identity()["url"]}}" title="{{get_identity()["name"]}} website"
                           class="website-link">
                            {{get_identity()["website"]}}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
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
</div>

@include('admin.templates.underscore_needle')
@include('admin.templates.modals.confirm_modal')

<script data-main="/admin_dashboard/js/all-23-07-18r2" src="/admin_dashboard/vendor/requirejs/require.js"></script>

</body>
</html>
