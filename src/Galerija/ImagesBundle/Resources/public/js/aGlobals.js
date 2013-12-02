(function ($) {
    /*
    * Globalūs kintamieji
    */
    $.globals = {

        /**
         * Priverčia metodą dirbti reikiamam kontekste
         * @namespace globals
         * @method bind
         * @param {Function} fn
         * @param {Object} obj
         */
        bind : function bind(fn, obj) {
            return function () {
                fn.apply(obj, arguments);
            };
        },
        /**
         * Parodyti žinutę ir ją pašalinti
         * @namespace globals
         * @method showStatus
         * @param {String} status
         * @param {String} msg
         */
        showStatus : function showStatus(status, msg) {
            var div = $('<div class="flash" id="' + (status ? 'success' : 'error') + '">' + msg + '</div>');
            $(".content").prepend(div);
            div.delay(3000).fadeOut(2000, function () {
                div.remove();
            });
        },
        container: $('.list'),
        /**
         * Fancybox paveikslėlių atvaizdavimui
         * @namespace globals
         * @method RefreshFancybox
         */
        RefreshFancybox: function()
        {
            $(".fancybox").fancybox({
                fitToView	: false,
                autoSize: false,
                autoDimensions: false,
                width: '90%',
                height: '90%',
                openEffect	: 'none',
                closeEffect	: 'none',
                scrolling : 'no',
                minWidth: '700px',
                minHeight: '400px',
                afterShow: function(){
                    //Pridedam reikalingus widget'us ir bind'us
                    $("#form_comment_add").PostComment({});
                    $("#like_area").LikeImage({});
                    $("#image_accordion").accordion({
                        heightStyle: "content"
                    });
                    $(".delete-comment").Delete({
                        type: 'comment',
                        route: 'galerija_comment_delete'
                    });
                    $(".fancybox-next").css({
                        right: "30%"
                    });
                    $( ".fancybox-nav, .block" ).hover(
                        function() {
                            $(".image_texts").show();
                        }, function() {
                            $(".image_texts").hide();
                        }
                    );
                }
            });
        }
    };
})(jQuery);