require.config({
    paths: {
        jquery: '/admin_dashboard/vendor/jquery/dist/jquery.min',
        jqueryUi: '/admin_dashboard/vendor/jquery-ui/jquery-ui',
        bootstrap: '/admin_dashboard/vendor/bootstrap/dist/js/bootstrap.min',
        underscore: '/admin_dashboard/vendor/underscore/underscore-min',
        select2: '/admin_dashboard/vendor/select2/dist/js/select2',
        codemirror: '/admin_dashboard/vendor/codemirror/lib/codemirror',
        tinymce: '/admin_dashboard/vendor/tinymce/js/tinymce/tinymce.min',
        codeBase: '/admin_dashboard/vendor/codeBase/codebase.min',
        hamster: '/admin_dashboard/vendor/hamster',
        persianDate: '/admin_dashboard/vendor/persianDate/persian-date.min',
        persianDatepicker: '/admin_dashboard/vendor/persianDatepicker/js/persian-datepicker',
        formBuilder: '/admin_dashboard/vendor/form-builder/form-builder.min',
        chartJs: '/admin_dashboard/vendor/chart-js/chart.min',
        iconPicker: '/admin_dashboard/vendor/fontIconPicker/dist/js/jquery.fonticonpicker',
        spectrum: '/admin_dashboard/vendor/spectrum/spectrum',
        toast: '/admin_dashboard/vendor/jquery-toast/jquery.toast.min',

    },
    shim: {
        "jqueryUi": {
            deps: ["jquery"]
        },
        "bootstrap": {
            deps: ["jquery"]
        },
        "toast": {
            deps: ["jquery"]
        },
        "codeBase": {
            deps: ["jquery"]
        },
        "codemirror": {
            deps: ["jquery"]
        },
        "persianDatepicker": {
            deps: ["jquery", "persianDate", "hamster"]
        },
        "select2": {
            deps: ["jquery"]
        },
        "formBuilder": {
            deps: ["jqueryUi"]
        },
        /*"chartJs": {
            deps: ["jquery"]
        }*/
    }
});
