(function ($) {
    /*
     * Profilio meniu
     */
    $(document).ready(function () {

        $(".fancybox").fancybox({
            fitToView	: false,
            autoSize: false,
            autoDimensions: false,
            width: '70%',
            height: '70%',
            openEffect	: 'none',
            closeEffect	: 'none',
            scrolling : 'no',
            minWidth: '700px',
            minHeight: '400px',
            wrapCSS : 'myclass',
            afterLoad: function(){
                $("#").PostComment({});
            }
        });

        $("#user_open").fancybox({
            'scrolling': 'no',
            'titleShow': false
        });


    });


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
        srcPath: ""
    };

    Delete.prototype._create = function () {
        //pakeičiam paveiksliuką pakeisdami tik jų galą, todėl išsisaugom tik direktorijas
        this.options.srcPath = this.element.attr("src").match(/[^\.]+\//);
        this._on(this.element, {
            "click": "start"
        });
    };

    Delete.prototype._initiate = function () {
        this.options.inProgress = true;
        //panaikinam "pranykimo" klase, kad paveiksliukas nepradingtu
        this.element.removeClass("disappear");
        this.element.attr("src", this.options.srcPath + "loading.gif");
    };

    Delete.prototype._result = function (data) {
        if (data.success) {
            this.element.parent().remove();
        }
        else {
            //sugrąžinam į pradinę būseną
            this._failure();

        }
        showStatus(data.success, data.message);
        this.options.inProgress = false;
    };

    Delete.prototype._failure = function () {
        this.element.attr("src", this.options.srcPath + "delete.png");
        this.element.addClass("disappear");
    };

    Delete.prototype.start = function () {
        //jei siuntimas jau pradėtas sustabdyti
        if (this.options.inProgress)
            return;

        $.ajax({
            url: Routing.generate('galerija_images_delete'),
            type: "POST",
            data: { "ID": this.element.attr('id') },
            context: this,
            beforeSend: this._initiate,
            error: function (xhr, ajaxOptions, thrownError) {
                showStatus(0, 'Ištrinti paveiksliuko nepavyko');
                this._failure();
            },
            success: this._result
        });
    };

    $.widget("custom.Delete", Delete.prototype);

    //pridedam widgetus prie elemtų
    $(".delete").Delete({});

    /*
     * Picture add
     */
    function Upload() {
    }

    Upload.prototype.options = {
        inProgress: false
    };

    Upload.prototype._create = function () {
        //randam baro containeri, kad būtų galimą visą paslėpti
        this.pBarContainer = this.element.find('.meter');
        this.pBar = this.pBarContainer.find('span');
        this.sButton = this.element.find('#image_upload_Ikelti');
        //uploadProgress veikia ne tame kontekste todėl pribindinam updateProgress
        this._updateProgress = bind(this._updateProgress, this);
        /*
         *ajaxForm - http://malsup.com/jquery/form/, galima sužinoti įkelimo procentus
         * išsiunčiama nuspaudus <input type="submit">
         */
        this.send = $('#form_image_add').ajaxForm({
            context: this,
            beforeSend: this._initiate,
            uploadProgress: this._updateProgress,
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
        if (data.success && data.name !== undefined) {
            //įdedam naują paveiksliuką
            var fullimg = $('<div class="image"><a class="fancybox" rel="gallery1" href="' + data.path + '" title="' + data.name + '">' +
                '<img alt="' + data.name + '" src="' + data.thumb_path + '"/></a>' +
                '<img class="delete disappear" id="' + data.ID + '" alt="Delete" src="' + data.delpath + '"></div>');
            $(".imglist").append(fullimg);

            //pridedam widgetus prie naujo elemento
            fullimg.find('.delete').Delete({});
            fullimg.find('.fancybox').fancybox({
                openEffect: 'none',
                closeEffect: 'none'
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

    //pridedam widgetus prie elemtų
    $("#form_image_add").Upload({});

    //post Comment widget
    function PostComment() {
    }

    PostComment.prototype.options = {
        inProgress: false
    };

    PostComment.prototype._create = function () {
        this.sButton = this.element.find('#album_create_Ikelti');
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
            //įdedam naują paveiksliuką
            var comment = $('<fieldset><legend>'+ data.username + '</legend>'+ data.value + '</fieldset>');
            $(".comments").find("ul").append(comment);
        }

        showStatus(data.success, data.message);
        this._hide();
    };
    PostComment.prototype._hide = function () {
        this.options.inProgress = false;
        this.sButton.prop("disabled", false);
    };
    $.widget("custom.PostComment", PostComment.prototype);
    //show Image info widget
    /*function Info() {
    }

    Info.prototype._create = function () {
        this._on(this.element, {
            "click": "start"
        });
    };

    Info.prototype.start = function () {
        event.preventDefault();
        $.ajax({
            beforeSend: $.fancybox.showLoading(),
            url: this.element.attr('href'),
            type: "POST",
            success: function (data) {
                $.fancybox(data )
            }
        });
    };

    $.widget("custom.Info", Info.prototype);*/

    //pridedam widgetus prie elemtų
    //$(".fancybox").Info({});

})(jQuery);