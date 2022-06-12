define("persian_number", ["jquery", "tools"], function(jQuery, tools){
    jQuery.fn.persianNumber = function(){
        this.each(function(){
            const thisEl = jQuery(this);
            thisEl.text(tools.convertNumberToPersian(thisEl.text()));
        });
    }
});