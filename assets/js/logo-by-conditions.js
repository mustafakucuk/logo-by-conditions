jQuery(document).ready(function($){
    var settings = logo_by_conditions;
    if( settings.auto_changer ) {
        var logo_selector = $(settings.logo_selector);
        if( logo_selector.is('img') ) {
            logo_selector.attr('src', settings.logo_url)
        }else{
            logo_selector.find('img').attr('src', settings.logo_url)
        }
    }
});