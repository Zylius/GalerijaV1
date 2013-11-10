(function( $ ) {
    /*
     * Profilio meniu
     */
    $(document).ready(function(){
        $(".topmenu_user,#overlay").click(function(){
            event.preventDefault();
            showDiv();
        })
    });

    function showDiv() {
        div = document.getElementById('overlay_wrapper');
        if(div.style.display == "block")
            div.style.display = "none";
        else
            div.style.display = "block";
    }

    /*
     * Sukuria statuso žinutę
     */
    function showStatus(status, msg)
    {
        var div = $('<div class="flash" id="'+ (status?'success':'error') + '">' + msg + '</div>');
        $( ".content" ).prepend(div);
        div.delay(3000).fadeOut(2000);
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
            this.element.attr("src", this.options.srcPath + "delete.png");
            this.element.addClass("disappear");
        }
        showStatus(data.success,  data.message);
        this.options.inProgress = false;
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
            beforeSend: this._initiate(),
            error: function (xhr, ajaxOptions, thrownError) {
                showStatus(0, 'Ištrinti paveiksliuko nepavyko');
            },
            success: function(data) {
                this._result(data);
            }
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

    var pBarContainer, sButton, pBar;

    Upload.prototype._create = function() {
        //randam baro containeri, kad būtų galimą visą paslėpti
        pBarContainer = this.element.find('.meter');
        pBar = pBarContainer.find('span');
        sButton = this.element.find('#image_upload_Ikelti');

        //ajaxForm - http://malsup.com/jquery/form/, galima sužinoti įkelimo procentus
        $( '#form_image_add').ajaxForm({
            context: this,
            beforeSend: function() {
                this._initiate();
            },
            uploadProgress: function(event, position, total, percentComplete) {
                pBar.width( percentComplete + '%');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                showStatus(0, 'Įkelti paveiksliuko nepavyko.');
            },
            success: function(data) {
                this._result(data);
            }
        });

    };

    Upload.prototype._initiate = function ()
    {
        pBar.width('0%');
        this.options.inProgress = true;
        sButton.prop("disabled",true);
        pBarContainer.show();
    };

    Upload.prototype._result = function(data)
    {
        if(data.success && data.name !== undefined)
        {
            //įdedam naują paveiksliuką
            var fullimg = $('<div class="image"><img alt="' + data.name + '" src="' + data.path + '"/>');
            fullimg.append('<img class="delete disappear" id="' + data.ID + '" alt="Delete" src="' + data.delpath + '"></div>');
            $(".imglist" ).append(fullimg);

            //pridedam delete widgetus prie naujų elementų
            $(".delete").Delete({});
        }

        showStatus(data.success, data.message);
        this.options.inProgress = false;

        sButton.prop("disabled",false);

        pBarContainer.hide(200);
    };

    $.widget("custom.Upload",Upload.prototype);

    //pridedam widgetus prie elemtų
    $("#form_image_add").Upload({});

})( jQuery );