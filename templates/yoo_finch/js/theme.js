/* Copyright (C) YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

jQuery(function($) {

    var config = $('html').data('config') || {};
    navbar = $('.tm-navbar');

    // Social buttons
    $('article[data-permalink]').socialButtons(config);

    // push footer to bottom of page
    UIkit.$win.on('load resize', UIkit.Utils.debounce((function(footer, fn, height, initTop) {

        footer  =  $('#tm-footer');

        if (!footer.length) return function(){};

        fn = function() {

            footer.css('margin-top', '');
            initTop = parseInt(footer.css('margin-top'), 10);

            if ((footer.offset().top <= window.innerHeight - footer.outerHeight())) {
                height = window.innerHeight - (footer.offset().top + footer.outerHeight()) + initTop;
                footer.css('margin-top', height);
            }

            return fn;
        };

        return fn();

    })(), 100));

});
