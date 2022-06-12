require(["jquery", "persianDate", "hamster"], function (jQuery, persianDate, Hamster) {
    window.persianDate = persianDate;
    window.Hamster = Hamster;
    require(["persianDatepicker"], function () {
        let datepickers = jQuery("[name$='_datepicker']");
        datepickers.each(function () {
            let thisEl = jQuery(this);
            const altName = thisEl.attr('name').replace("_datepicker", "_date");
            thisEl.persianDatepicker({
                format: 'YYYY/MM/DD',
                altField: 'input[name="' + altName + '"]',
                altFieldFormatter: function(unix){
                    let date = new Date(unix);
                    return date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate()+" 23:59:59";
                }
            });
        });
    });
});