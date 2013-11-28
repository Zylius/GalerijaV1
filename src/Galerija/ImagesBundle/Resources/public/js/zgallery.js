(function ($) {
    /*
     * Profilio meniu
     */
    $(document).ready(function () {

        RefreshFancybox();
        $(".flash").delay(3000).fadeOut(2000, function () {
            div.remove();
        });
        $("#user_open").fancybox({
            'scrolling': 'no',
            fitToView	: true,
            autoSize: true,
            autoDimensions: true,
            minWidth: '350px',
            'titleShow': false
        });
        $("#form_array").accordion({
            heightStyle: "content"
        });

        $('#tag_filter').bind('input propertychange', function() {
            var input = $(this).val();
            if(input != '')
            {
                $container.isotope({ filter: '.tag-' + input });
            }
            else
            {
                $container.isotope({ filter: '*' });
            }
        });


    });

    var RefreshFancybox = function()
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
                $( ".fancybox-nav" ).hover(
                    function() {
                        $(".image_texts").show();
                    }, function() {
                        $(".image_texts").hide();
                    }
                );
            }
        });
    };
    var $container = $('.list');

    $container.imagesLoaded(function () {
        $container.isotope({
            resizable: false,
            masonry: { columnWidth: $container.width() / 100 },
            animationEngine: 'jquery'
        });
    });

    $(window).smartresize(function () {
        $.fancybox.update();
        $container.isotope({
            masonry: { columnWidth: $container.width() / 100 }
        });
    });

    function bind(fn, obj) {
        return function () {
            fn.apply(obj, arguments);
        };
    };


    /*
     * Sukuria statuso žinutę
     */
    function showStatus(status, msg) {
        var div = $('<div class="flash" id="' + (status ? 'success' : 'error') + '">' + msg + '</div>');
        $(".content").prepend(div);
        div.delay(3000).fadeOut(2000, function () {
            div.remove();
        });
    }

    /*
     * Pic delete widget
     */
    function Delete() {
    }

    Delete.prototype.options = {
        inProgress: false,
        aID: 0,
        type: 'image',
        route: 'galerija_images_delete'
    };

    Delete.prototype._create = function () {
        this._on(this.element, {
           "click": "_prompt"
        });
        this.deleteAll = bind(this.deleteAll, this);
        this.deleteSingle = bind(this.deleteSingle, this);
        this.start = bind(this.start, this);
        this.srcPath = this.element.attr("src").replace(/[^\/]*$/,"");
        this.csrf_token = this.element.attr("data-csrf_token");
    };
    Delete.prototype._prompt = function () {
        //jei siuntimas jau pradėtas sustabdyti
        if (this.options.inProgress)
            return;
        if(this.options.type == 'comment')
        {
            this.start(null);
            return;
        }
        if(this.options.type == 'album')
        {
            $("#delete-dialog" ).dialog(
                {
                    modal:true, //Not necessary but dims the page background
                    width: '25em',
                    resizable: false,
                    context: this,
                    buttons:{
                        'Patvirtinti': this.start
                    }
                }

            );
            return;
        }
        $("#delete-dialog" ).dialog(
            {
                modal:true, //Not necessary but dims the page background
                width: '25em',
                resizable: false,
                context: this,
                buttons:{
                    'Iš visų albumų': this.deleteAll,
                    'Tik iš šio albumo':this.deleteSingle
                }
            }

        );
        if(this.options.aID == 0)
        {
            $(".ui-dialog-buttonpane button:contains('Tik iš šio albumo')").attr("disabled", true).addClass("ui-state-disabled");
        }
    };
    Delete.prototype.deleteAll = function ()
    {
        this.start(null, 0);
    }
    Delete.prototype.deleteSingle = function ()
    {
        this.start(null, this.options.aID);
    }
    Delete.prototype._initiate = function () {
        //pakeičiam paveiksliuką pakeisdami tik jų galą, todėl išsisaugom tik direktorijas

        this.options.inProgress = true;
        //panaikinam "pranykimo" klase, kad paveiksliukas nepradingtu
        this.element.removeClass("disappear");
        this.element.attr("src", this.srcPath + "loading.gif");
    };

    Delete.prototype._result = function (data) {
        if (data.success) {
            $container.isotope('remove', this.element.parent());
        }
        else {
            //sugr?žinam ? pradin? b?sen?
            this._failure();

        }
        showStatus(data.success, data.message);

        this.options.inProgress = false;
    };

    Delete.prototype._failure = function () {
        this.element.attr("src", this.srcPath + "delete.png");
        this.element.addClass("disappear");
    };

    Delete.prototype.start = function (event, extraData) {

        var dialog_container = $("#delete-dialog");
        if( dialog_container.hasClass('ui-dialog-content'))
            dialog_container.dialog( "close" );
        extraData = extraData || 0;
        $.ajax({
            url: Routing.generate(this.options.route),
            type: "POST",
            data: {
                "ID": this.element.attr('id'),
                "aID": extraData,
                "csrf_token": this.csrf_token
            },
            context: this,
            beforeSend: this._initiate,
            error: function (xhr, ajaxOptions, thrownError) {
                showStatus(0, 'Ištrinti nepavyko');
                this._failure();
            },
            success: this._result
        });
    };

    $.widget("custom.Delete", Delete.prototype);

    //pridedam widgetus prie elemt?
    $(".delete-image").Delete({ aID: $container.attr('aID') });
    $(".delete-album").Delete({
        type: 'album',
        route: 'galerija_album_delete'
    });
    /*
     * Picture add
     */
    function Upload() {
    }

    Upload.prototype.options = {
        inProgress: false
    };

    Upload.prototype._create = function () {
        //randam baro containeri, kad b?t? galim? vis? pasl?pti
        this.pBarContainer = this.element.find('.meter');
        this.pBar = this.pBarContainer.find('span');
        this.sButton = this.element.find('#image_upload_Ikelti');
        //uploadProgress veikia ne tame kontekste tod?l pribindinam updateProgress
        this._updateProgress = bind(this._updateProgress, this);
        /*
         *ajaxForm - http://malsup.com/jquery/form/, galima sužinoti ?kelimo procentus
         * išsiun?iama nuspaudus <input type="submit">
         */
        this.send = $('#form_image_add').ajaxForm({
            context: this,
            beforeSend: this._initiate,
            uploadProgress: this._updateProgress,
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            error: function (xhr, ajaxOptions, thrownError) {
                showStatus(0, 'Įkelti paveiksliuko nepavyko.');
                this._hide();
            },
            success: this._result
        });
    };

    Upload.prototype._updateProgress = function (event, position, total, percentComplete) {
        this.pBar.width(percentComplete + '%');
    };

    Upload.prototype._initiate = function () {
        this.pBar.width('0%');
        this.options.inProgress = true;
        this.sButton.prop("disabled", true);
        this.pBarContainer.show();
    };

    Upload.prototype._result = function (data) {
        if (data.success && data.value !== undefined) {

            //?dedam nauj? paveiksliuk?
            var fullimg = $('<div>',{html:data.value});
            $('.list').append(fullimg);
            //pridedam widgetus prie naujo elemento
            fullimg.find('.delete').Delete({ aID: $container.attr('aID') });
            RefreshFancybox();
            fullimg.imagesLoaded(function(){
                $container.isotope( 'insert', fullimg );
            });
        }

        showStatus(data.success, data.message);
        this._hide();
    };

    Upload.prototype._hide = function () {
        this.options.inProgress = false;

        this.sButton.prop("disabled", false);

        this.pBarContainer.hide(200);
    };
    $.widget("custom.Upload", Upload.prototype);

    //pridedam widgetus prie elemt?
    $("#form_image_add").Upload({});

    //post Comment widget
    function PostComment() {
    }

    PostComment.prototype.options = {
        inProgress: false
    };

    PostComment.prototype._create = function () {
        this.sButton = this.element.find('#comment_post_Ikelti');
        this.send = this.element.ajaxForm({
            context: this,
            beforeSend: this._initiate,
            error: function (xhr, ajaxOptions, thrownError) {
                showStatus(0, 'Komentuoti nepavyko.');
                this._hide();
            },
            success: this._result
        });
    };


    PostComment.prototype._initiate = function () {
        this.sButton.prop("disabled", true);
        this.options.inProgress = true;
    };

    PostComment.prototype._result = function (data) {
        if (data.success) {
            //?dedam nauj? paveiksliuk?
            var comment = $('<li><img class="delete disappear delete-comment" id="' + data.id + '"' +
            'alt="Delete" src="' + data.delpath + '" data-csrf_token="'+ data.token +'"/>' +
            '<fieldset><legend>'+ data.username + '</legend>'+ data.value + '</fieldset></li>');
            $(".comments").find("ul").append(comment);
            comment.find('img').Delete({
                type: 'comment',
                route: 'galerija_comment_delete'
            });
        }
        showStatus(data.success, data.message);
        this._hide();
    };
    PostComment.prototype._hide = function () {
        this.options.inProgress = false;
        this.sButton.prop("disabled", false);
    };

    $.widget("custom.PostComment", PostComment.prototype);

    //"palikinimo" widgetas
    function LikeImage(){}

    LikeImage.prototype.options = {
        inProgress: false
    };

    LikeImage.prototype._create = function () {
        this.press = this.element.find('a');
        this._on(this.press, {
            "click": "start"
        });
        this.countField = this.element.find('span');
        this.lButton = this.element.find('img');
        this.Status = !!(this.element.attr('like_status') == "true");
        this.srcPath = this.lButton.attr("src").replace(/[^\/]*$/,"");
    };

    LikeImage.prototype.start = function () {
        //jei siuntimas jau prad?tas sustabdyti
        if (this.options.inProgress)
            return false;

        $.ajax({
            url: this.press.attr('href'),
            type: "GET",
            context: this,
            beforeSend: this._initiate,
            error: function (xhr, ajaxOptions, thrownError) {
                showStatus(0, '"Like\'inti" gali tik prisijung? vartotojai.');
                this._failure();
            },
            success: this._result
        });
        return false;
    };

    LikeImage.prototype._initiate = function () {
        this.options.inProgress = true;
    };

    LikeImage.prototype._result = function (data) {
        if (data.success) {
            this.switch();
            this.countField.html(data.count);
        }
        this.options.inProgress = false;
    };

    LikeImage.prototype._failure = function () {
        this.options.inProgress = false;
    };

    LikeImage.prototype.switch = function()
    {
        if(this.Status)
        {
            this.lButton.attr("src", this.srcPath + "like.png");
            this.Status = false;
        }
        else
        {
            this.lButton.attr("src", this.srcPath + "like-ok.png");
            this.Status = true;
        }
    }
    $.widget("custom.LikeImage", LikeImage.prototype);

    //nustato kaip titulinį image albumui
    function DefaultImage(){}

    DefaultImage.prototype.options = {
        inProgress: false,
        aID: 0
    };

    DefaultImage.prototype._create = function () {

        this.CSRF = this.element.attr('data-csrf_token');
        this.ID = this.element.attr('data-id');
        this._on(this.element, {
            "click": "start"
        });
    };

    DefaultImage.prototype.start = function () {
        //jei siuntimas jau prad?tas sustabdyti
        if (this.options.inProgress)
            return false;

        $.ajax({
            url: Routing.generate("galerija_album_default"),
            type: "POST",
            data: {
                "ID": this.ID,
                "aID": this.options.aID,
                "csrf_token": this.CSRF
            },
            context: this,
            beforeSend: this._initiate,
            error: function (xhr, ajaxOptions, thrownError) {
                showStatus(0, 'Klaida!');
                this._failure();
            },
            success: this._result
        });
        return false;
    };

    DefaultImage.prototype._initiate = function () {
        this.options.inProgress = true;
    };

    DefaultImage.prototype._result = function (data) {
        showStatus(data.success, data.message);
        this.options.inProgress = false;
    };

    DefaultImage.prototype._failure = function () {
        this.options.inProgress = false;
    };
    $.widget("custom.DefaultImage", DefaultImage.prototype);
    $(".make_default").DefaultImage({ aID: $container.attr('aID') });
})(jQuery);