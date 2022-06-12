define('attachable', ['jquery', 'form_control', 'select2'], function (jQuery) {
    let attr_product = [],
        attr_name,
        attr_fields,
        attr_text = '',
        directoryListElement = jQuery('#directory-list');

    directoryListElement.select2({
        'placeholder':'دسته بندی'
    });
    (function (jQuery) {
        jQuery.fn.attachable = function () {
            this.formControl();
            this.on('model:attach', function (event, data) {
                var thisEl = jQuery(this);
                if (thisEl.data('parent-name')) {
                    attr_name = thisEl.data('parent-name');
                    attr_fields = thisEl.data('attr-text');
                    if (attr_fields != 1)
                        attr_fields = thisEl.data('attr-text');
                    else
                        attr_fields = [];
                    if (localStorage.getItem('attr-product'))
                        attr_fields = JSON.parse(localStorage.getItem('attr-product'));

                    localStorage.setItem('attr-product', JSON.stringify(attr_fields));
                    setAttrProduct('attach', data.name, attr_name);
                    attr_text = JSON.stringify(localStorage.getItem('attr-product'));
                }
                jQuery.ajax({
                    url: thisEl.data('attach'),
                    method: 'POST',
                    data: {
                        id: data.id,
                        data_text: attr_text,
                        _token: window.csrf_token
                    }
                }).done(function (result) {
                    try {
                        thisEl.trigger('message:success', result.transmission.messages[0]);
                    } catch (e) {
                        console.log(e);
                        console.log(result);
                    }
                });
            }).on('model:detach', function (event, data) {
                var thisEl = jQuery(this);
                if (thisEl.data('parent-name')) {
                    attr_name = thisEl.data('parent-name');
                    attr_fields = thisEl.data('attr-text');
                    if (attr_fields != 1)
                        attr_fields = thisEl.data('attr-text');
                    else
                        attr_fields = [];
                    if (localStorage.getItem('attr-product'))
                        attr_fields = JSON.parse(localStorage.getItem('attr-product'));

                    localStorage.setItem('attr-product', JSON.stringify(attr_fields));
                    setAttrProduct('detach', data.name, attr_name);
                    attr_text = JSON.stringify(localStorage.getItem('attr-product'));
                }

                jQuery.ajax({
                    url: thisEl.data('detach'),
                    method: 'POST',
                    data: {
                        id: data.id,
                        data_text: attr_text,
                        _token: window.csrf_token
                    }
                }).done(function (result) {
                    try {
                        thisEl.trigger('message:warning', result.transmission.messages[0]);
                    } catch (e) {
                        console.log(e);
                        console.log(result);
                    }
                });
            });
        };
    })(jQuery);
    // write by jafar choupan
    window.onload = function () {
        localStorage.removeItem('attr-product');
    };

    function setAttrProduct(checker, data, attr_name) {
        let attr_content = [];
        if (localStorage.getItem('attr-product') && (JSON.parse(localStorage.getItem('attr-product')).length > 0)) {
            attr_product = JSON.parse(localStorage.getItem('attr-product'));
            attr_product = attr_product.filter(item => {
                if (item.attr_name === attr_name) {
                    attr_content = item.attr_content;
                }
                return item.attr_name != attr_name;
            });
        } else {
            attr_product = [];
        }
        if (checker === 'attach') {
            attr_content.push(data);
            attr_product.push({
                attr_name: attr_name,
                attr_content: attr_content,
            })
        } else {
            let index = attr_content.indexOf(data);
            if (index > -1) {
                attr_content.splice(index, 1);
            }
            attr_product.push({
                attr_name: attr_name,
                attr_content: attr_content,
            })
        }
        localStorage.setItem('attr-product', JSON.stringify(attr_product));
    }
});