define('tools', ['jquery', 'underscore'], function (jQuery, _) {

    return {
        dropNonDigits: function (string) {
            var digits = '0123456789۰۱۲۳۴۵۶۷۸۹';
            var result = '';
            _.each(string, function (item) {
                if (_.indexOf(digits, item) !== -1) {
                    result += item;
                }
            });
            return result;
        },
        convertNumberToPersian: function (string) {
            var persianDigits = '۰۱۲۳۴۵۶۷۸۹';
            var englishDigits = '0123456789';

            _.each(englishDigits, function (item, index, list) {
                string = string.split(item).join(persianDigits[index]);
            });

            return string;
        },
        convertNumberToEnglish: function (string) {
            var persianDigits = '۰۱۲۳۴۵۶۷۸۹';
            var englishDigits = '0123456789';

            _.each(persianDigits, function (item, index, list) {
                string = string.split(item).join(englishDigits[index]);
            });

            return string;
        },
        formatNumber: function (string) {
            var length = string.length;
            var temp = length % 3;
            var result = '';

            _.each(string, function (item, index, list) {
                if (index === temp) {
                    if (index !== 0)
                        result += ',';
                    if (temp + 3 < length)
                        temp += 3;
                }
                result += item;
            });
            return result;
        },
        getMainForm: function () {
            return jQuery('form[act="main-form"]');
        },
        isInOffset: function (offset, point) {
            return (
                (point.x < offset.right) &&
                (point.x > offset.left) &&
                (point.y < offset.bottom) &&
                (point.y > offset.top)
            );
        },
        httpRequest: function (api, body, method, functionEnter, needToken, option = '') {
            jQuery.ajax({
                url: api,
                method: method,
                data: body
            }).success(function (result) {
                return functionEnter(result, option);
            }).error(function (error) {
                console.log(error);
            });
        },
        openLink: function (href) {
            location.href = href || '#';
        }

    };


});