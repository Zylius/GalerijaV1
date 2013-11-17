(function( $ ) {
    /*
     * Profilio meniu
     */
    $(document).ready(function(){
        $(".topmenu_user,#overlay").click(function(){
            showDiv();
            //IE fix
            event.preventDefault ? event.preventDefault() : event.returnValue = false;
        })
        $(".fancybox").fancybox({
            openEffect	: 'none',
            closeEffect	: 'none'
        });
    });

    function showDiv() {
        div = document.getElementById('overlay_wrapper');
        if(div.style.display == "block")
            div.style.display = "none";
        else
            div.style.display = "block";
    }

    function bind(fn,obj) {
        return function() {
            fn.apply(obj,arguments);
        };
    };



    /*
     * Sukuria statuso žinutę
     */
    function showStatus(status, msg)
    {
        var div = $('<div class="flash" id="'+ (status?'success':'error') + '">' + msg + '</div>');
        $( ".content" ).prepend(div);
        div.delay(3000).fadeOut(2000, function() { div.remove(); });
    }

    /*
     * Pic delete widget
     */
    function Delete() {}

    Delete.prototype.options = {
        inProgress : false,
        srcPath : ""
    };

    Delete.prototype._create = function() {
        //pakeičiam paveiksliuką pakeisdami tik jų galą, todėl išsisaugom tik direktorijas
        this.options.srcPath = this.element.attr("src").match(/[^\.]+\//);
        this._on(this.element,{
            "click" : "start"
        });
    };

    Delete.prototype._initiate = function ()
    {
        this.options.inProgress = true;
        //panaikinam "pranykimo" klase, kad paveiksliukas nepradingtu
        this.element.removeClass("disappear");
        this.element.attr("src", this.options.srcPath + "loading.gif");
    };

    Delete.prototype._result = function(data)
    {
        if(data.success)
        {
            this.element.parent().remove();
        }
        else
        {
            //sugrąžinam į pradinę būseną
            this._failure();

        }
        showStatus(data.success,  data.message);
        this.options.inProgress = false;
    };

    Delete.prototype._failure = function()
    {
        this.element.attr("src", this.options.srcPath + "delete.png");
        this.element.addClass("disappear");
    };

    Delete.prototype.start = function() {
        //jei siuntimas jau pradėtas sustabdyti
        if(this.options.inProgress)
            return;

        $.ajax({
            url: Routing.generate('galerija_images_delete'),
            type: "POST",
            data: { "ID" : this.element.attr('id') },
            context: this,
            beforeSend: this._initiate,
            error: function (xhr, ajaxOptions, thrownError) {
                showStatus(0, 'Ištrinti paveiksliuko nepavyko');
                this._failure();
            },
            success: this._result
        });
    };

    $.widget("custom.Delete",Delete.prototype);

    //pridedam widgetus prie elemtų
    $(".delete").Delete({});

     /*
     * Picture add
     */
    function Upload() {}

    Upload.prototype.options = {
        inProgress : false
    };

    Upload.prototype._create = function() {
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
        this.send = $( '#form_image_add').ajaxForm({
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

    Upload.prototype._updateProgress = function(event, position, total, percentComplete)
    {
        this.pBar.width(percentComplete + '%');
    }

    Upload.prototype._initiate = function ()
    {
        this.pBar.width('0%');
        this.options.inProgress = true;
        this.sButton.prop("disabled",true);
        this.pBarContainer.show();
    };

    Upload.prototype._result = function(data)
    {
        if(data.success && data.name !== undefined)
        {
            //įdedam naują paveiksliuką
            var fullimg = $('<div class="image"><a class="fancybox" rel="gallery1" href="' + data.path + '" title="' + data.name + '">' +
                '<img alt="' + data.name + '" src="' + data.thumb_path + '"/></a>' +
                '<img class="delete disappear" id="' + data.ID + '" alt="Delete" src="' + data.delpath + '"></div>');
            $(".imglist" ).append(fullimg);

            //pridedam widgetus prie naujo elemento
            fullimg.find('.delete').Delete({});
            fullimg.find('.fancybox').fancybox({
                openEffect	: 'none',
                closeEffect	: 'none'
            });

        }

        showStatus(data.success, data.message);
        this._hide();
    };
    Upload.prototype._hide = function()
    {
        this.options.inProgress = false;

        this.sButton.prop("disabled",false);

        this.pBarContainer.hide(200);
    }
    $.widget("custom.Upload",Upload.prototype);

    //pridedam widgetus prie elemtų
    $("#form_image_add").Upload({});

})( jQuery );