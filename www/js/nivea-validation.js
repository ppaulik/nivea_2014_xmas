/**
 *
 */

(function ($) {

    var messageWait = 2000;

    Nette.addError = function(elem, message) {
        if (elem.focus) {
            elem.focus();
        }
        if (message) {
            var element = $('.error-messages');
            if ($(element).length) {
                $(element).find('.message').text(message);
                $(element).css("display", 'block');
                setTimeout((function() {
                    $(element).animate({
                        opacity: 0
                    }, 1000, function() {
                        $(this).css({
                            opacity: "",
                            display: "none"
                        });
                    });
                }), messageWait);
            }
        }
    };

    $.fn.message = function() {
        var _this = this;
        $(this).css("display", 'block');
        setTimeout((function() {
            $(_this).animate({
                opacity: 0
            }, 1000, function() {
                $(_this).css({
                    opacity: "",
                    display: "none"
                });
            });
        }), messageWait);
    };

    $('.flash').message();

    $('.login').click(function() {
        $('.popup').addClass('active');
    });

})(jQuery);