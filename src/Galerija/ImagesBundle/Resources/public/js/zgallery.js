/*
 * Prideda visus pagrindinius puslapio widgetus, bindus ir t.t.
 */
(function ($) {
    // pažymima ar jau užkrauti visi paveiksliukai
    var DoneLoading = false;
    $(document).ready(function () {
        //pridedam fancybox
        $.globals.RefreshFancybox();

        //jei yra statuso žinutė, ją po kurio laiko panaikinam
        $(".flash").delay(3000).fadeOut(2000, function () {
            this.remove();
        });

        //formos su fancybox
        $("#upload_image, #user_open, .view_edit").fancybox({
            'scrolling': 'no',
            fitToView	: true,
            autoSize: true,
            autoDimensions: true,
            minWidth: '350px',
            'titleShow': false,
            afterShow: function()
            {
                AddSelectFix()
            }
        });

        //tag ir image_upload formų jQueryUI widgetas
        $("#form_array").accordion({
            heightStyle: "content"
        });

        //jei egzistuoja tag'ų paieškos filtras pridedam filtravimą pagal tag'us
        var tag_filter =  $('#tag_filter');
        if(tag_filter.length > 0)
        {
            tag_filter.bind('input propertychange', function() {
                var input = $(this).val();

                if(input != '')
                {
                    if(DoneLoading == false)
                    {
                        $.globals.container.infinitescroll('retrieve');
                    }
                    $.globals.container.isotope({ filter: '.tag-' + input });

                }
                else
                {
                    $.globals.container.isotope({ filter: '*' });
                }
            });
            tag_filter.autocomplete({
                source: tag_filter.attr('data-tags').split(','),
                select: function( event, ui ) {
                    if(!DoneLoading)
                        $.globals.container.infinitescroll('retrieve');
                    $.globals.container.isotope({ filter: '.tag-' + ui.item.value });
                }
            });
        }

        //pridedam custom widget'us: paveiksliukų ištrinimą, pridėjimą, titulinio nustatymą
        $(".make_default").DefaultImage({ aID: $.globals.container.attr('data-aID') });
        $(".delete-image").Delete({ aID: $.globals.container.attr('data-aID') });
        $(".delete-album").Delete({
            type: 'album',
            route: 'galerija_album_delete'
        });
        $("#form_image_add").Upload({});
    });

    //padaro kad ubūtų galima ne  tik pažymėti, bet ir "atžymėti"
    var AddSelectFix = function()
    {
        var tag_selections = $('select#image_upload_tags option');
        tag_selections.unbind('mousedown');
        tag_selections.mousedown(function () {
            if ($(this).prop("selected"))
                $(this).prop("selected", false);
            else
                $(this).prop("selected", "selected");
            return false;
        });
    };

    //pridedam isotope ir infinite scroll widgetus tik kai visi užkrauti
    $.globals.container.imagesLoaded(function () {
        $.globals.container.isotope({
            resizable: false,
            masonry: { columnWidth: $.globals.container.width() / 1000 },
            animationEngine: 'jquery'
        });

    $.globals.container.infinitescroll({
            navSelector  : "#page_nav",
            nextSelector : "#page_nav a",
            itemSelector : ".image",
            extraScrollPx: 150,
            prefill: true,
            loading: {
                msgText: 'Paveikslėliai kraunami.',
                finishedMsg: 'Visi paveiksliukai užkrauti.'
            },
           errorCallback: function(){
               DoneLoading = true;
           }
        },function(arrayOfNewElems){
            $(arrayOfNewElems).hide();
            $(arrayOfNewElems).find("img").each(function () {
                var originalSrc = $(this).attr('src');
                $(this).attr('src', originalSrc + "?" + new Date().getTime());
            });
            $(arrayOfNewElems).imagesLoaded(function(){
                $.globals.container.isotope( 'insert', $(arrayOfNewElems) );
                $(arrayOfNewElems).find('.delete-image').Delete({ aID: $.globals.container.attr('data-aID') });
                $(arrayOfNewElems).find(".make_default").DefaultImage({ aID: $.globals.container.attr('data-aID') });
                $(arrayOfNewElems).show();
            });
        });
    });

    //sutvarkom isotope ir fancybox responsive design bug'ą
    $(window).smartresize(function () {
        $.fancybox.update();
        $.globals.container.isotope({
            masonry: { columnWidth: $.globals.container.width() / 1000 }
        });
    });

})(jQuery);