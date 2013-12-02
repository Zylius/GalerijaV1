/*
 * Delete widget'as
 * Ištrina Komentarus, paveiksliukus, albumus.
 * Paveiksliukams ir albumams parodoma patvirtinimo lentelė
 * Trinant paveiksliukus galima pasirinkti, ar trinsim tik iš šio albumo ar iš visų
 * Option aID reikšmė nurodo, kuriame albume esame. Jei aID lygi nuliui, vadinasi albumas yra
 * sugeneruotas dinamiškai (pvz.: visos vartotojo nuotraukos) ir jį galima ištrinti tik iš visų albumų.
 */
(function ($) {
    function Delete() {
    }

    Delete.prototype.options = {
        aID: 0,
        type: 'image',
        route: 'galerija_image_delete'
    };

    Delete.prototype._create = function () {
        this.inProgress = false;
        this._on(this.element, {
            "click": "_prompt"
        });
        this.deleteAll = $.globals.bind(this.deleteAll, this);
        this.deleteSingle = $.globals.bind(this.deleteSingle, this);
        this.start = $.globals.bind(this.start, this);
        this.srcPath = this.element.attr("src").replace(/[^\/]*$/,"");
        this.csrf_token = this.element.attr("data-csrf_token");
    };

    Delete.prototype._prompt = function () {
        if (this.inProgress)
            return;

        if(this.options.type == 'comment')
        {
            this.start(null);
            return;
        }

        var delete_dialog = $("#delete-dialog" );

        if(this.options.type == 'album')
        {
            delete_dialog.dialog(
                {
                    modal:true,
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

        delete_dialog.dialog(
            {
                modal:true,
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
    };

    Delete.prototype.deleteSingle = function ()
    {
        this.start(null, this.options.aID);
    };

    Delete.prototype._initiate = function () {
        this.inProgress = true;
        this.element.removeClass("disappear");
        this.element.attr("src", this.srcPath + "loading.gif");
    };

    Delete.prototype._result = function (data) {
        if (data.success) {
            $.globals.container.isotope('remove', this.element.parent().parent(),
                function(){
                    $(window).smartresize();
                });
        }
        else {
            this._failure();
        }
        $.globals.showStatus(data.success, data.message);

        this.inProgress = false;
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
                "ID": this.element.attr('data-id'),
                "aID": extraData,
                "csrf_token": this.csrf_token
            },
            context: this,
            beforeSend: this._initiate,
            error: function (xhr, ajaxOptions, thrownError) {
                $.globals.showStatus(0, 'Ištrinti nepavyko');
                this._failure();
            },
            success: this._result
        });
    };

    $.widget("custom.Delete", Delete.prototype);
})(jQuery);