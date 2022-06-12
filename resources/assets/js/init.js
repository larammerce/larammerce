require(['jquery', 'search_module', 'bootstrap', 'jqueryUi'], function (jQuery, searchModule) {
    window.customConfirm = function (question, accepted, rejected) {
        window.acceptReq = function () {
            if (accepted)
                accepted();
        };
        window.rejectReq = function () {
            if (rejected)
                rejected();
        };
        var modalEl = jQuery('#confirm-modal');
        modalEl.find('p.message').text(question);
        modalEl.modal('show');
    };

    function squareRatio() {
        jQuery('.square-ratio').each(function () {
            var thisElement = jQuery(this);
            thisElement.css({
                height: thisElement.width() + 'px'
            });
        });
    }

    function formConfirm() {
        jQuery('form[confirm]').each(function (index) {
            var formEl = jQuery(this);
            formEl.find('button').on('click', function (event) {
                event.preventDefault();
                window.currentForm = formEl;
                window.customConfirm('آیا از انجام این عملیات اطمینان دارید ؟', window.submitForm);
                return false;
            });
        });
    }

    window.submitForm = function () {
        if (window.currentForm)
            window.currentForm.submit();
    };

    jQuery(document).ready(function () {
        squareRatio();
        formConfirm();
    });

    jQuery(window).resize(function () {
        squareRatio();
    });

    searchModule.init({
        inputSelector: "#search-input",
        mobileButtonSelector: "#search-button",
        resultRowSelector: ".result-row"
    });
});