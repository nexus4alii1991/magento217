define([
    'jquery',
    'jquery/ui',
    'mage/menu'],
    function($){
        $.widget('inchoo.menu', $.mage.menu, {
            _init: function () {
                //alert("I'm Inchoo");
            },
            toggle: function () {
                //alert("I'm Inchoo");
            }
    });
    return $.inchoo.menu;
    });