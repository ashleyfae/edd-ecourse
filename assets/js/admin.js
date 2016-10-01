jQuery(document).ready(function ($) {

    // Tooltips
    $('.edd-ecourse-tip').tooltip({
        content: function () {
            return $(this).prop('title');
        },
        tooltipClass: 'edd-ecourse-tooltip',
        position: {
            my: 'center top',
            at: 'center bottom+10',
            collision: 'flipfit'
        },
        hide: false,
        show: false
    });

});