(function ($) {
    'use strict';

    $(window).load(function () {
        $("#submit").click(function () {
            let missing_required = false
            let count = 0;
            $('input').filter('[required]').each(function () {
                if ($(this).val() == '') {
                    if (count == 0) {
                        $(this).focus();
                    }
                    missing_required = true;
                    $(this).addClass('yapt_required');
                    count++;
                } else {
                    $(this).removeClass('yapt_required');
                }
            });
            if (missing_required) {
                return false;
            }
            return true;
        });

        if ($('#defaultOpen').length) {
            document.getElementById("defaultOpen").click();
        }
    });

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */
})(jQuery);

function yapt_admin_tab(evt, tabId) {
    evt.preventDefault();
    // Declare all variables
    var i, tabcontent, tablinks;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    document.getElementById(tabId).style.display = "block";
    evt.currentTarget.className += " active";
}