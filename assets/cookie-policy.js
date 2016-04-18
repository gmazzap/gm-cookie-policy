(function($, document) {
    
    "use strict";

    $(document).on('click', '#cookie-policy-close', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        var $a = $(this),
            d = new Date(),
            now = d.getTime();

        d.setTime(now + ($a.data('cookie-expire') * 1000));

        $('#gm-cookie-policy').hide();

        document.cookie =
            $a.data('cookie-name')
            + "="
            + Math.floor(now / 1000)
            + "; expires=" + d.toUTCString();

        return false;
    });

})(jQuery, document);