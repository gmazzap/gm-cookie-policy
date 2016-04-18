var cookiePolicy = window.cookiePolicy || {};

(function ($, document, CP) {

    "use strict";

    CP.cookie = CP.cookie || {};
    CP.cookie.name = CP.cookie.name || '';
    CP.cookie.expire = CP.cookie.expire || 0;
    CP.accepted = CP.accepted || false;

    
    // When document is loaded we set `window.cookiePolicy.accepted` to proper value so it can be
    // checked but other scripts.
    $(document).ready(function () {
        var event = $.Event('cookie-policy-loaded');
        CP.accepted = document.cookie.indexOf(CP.cookie.name) > -1;
        // Other scripts can listen to this event to check user already accepted cookies.
        event.accepted = CP.accepted;
        $(document).trigger(event);
        console.log('Cookie policy loaded.');
    });


    // When user close the message, we hide it, set the cookie and fire a custom event
    $(document).on('click', '#cookie-policy-close', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        var d = new Date(), now = d.getTime(), value = Math.floor(now / 1000);
        d.setTime(now + (CP.cookie.expire * 1000));

        // Hide message
        $('#gm-cookie-policy').hide();

        // Set cookie
        document.cookie = CP.cookie.name + '=' + value + '; expires=' + d.toUTCString();

        // Fire event that can be used to initialize some features only when user accepted cookies.
        var event = $.Event('cookie-policy-accepted');
        event.cookie = CP.cookie;
        $(document).trigger(event);

        return false;
    });


    // When user accepts cookie policy, we update `window.cookiePolicy.accepted` var accordingly
    $(document).on('cookie-policy-accepted', function () {
        CP.accepted = true;
        console.log('Cookie policy accepted.');
    });

})(jQuery, document, cookiePolicy);
