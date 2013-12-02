/*
* DefaultImage widget'as
* nustato nuotrauką kaip titulinę albumo
*/
(function ($) {

    function DefaultImage(){}

    DefaultImage.prototype.options = {
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
        if (this.inProgress)
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
                $.globals.showStatus(0, 'Klaida!');
                this._failure();
            },
            success: this._result
        });
        return false;
    };

    DefaultImage.prototype._initiate = function () {
        this.inProgress = true;
    };

    DefaultImage.prototype._result = function (data) {
        $.globals.showStatus(data.success, data.message);
        this.inProgress = false;
    };

    DefaultImage.prototype._failure = function () {
        this.inProgress = false;
    };

    $.widget("custom.DefaultImage", DefaultImage.prototype);

})(jQuery);