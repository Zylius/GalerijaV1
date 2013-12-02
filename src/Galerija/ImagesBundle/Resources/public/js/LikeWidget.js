/*
 * LikeImage widget'as
 * "Palike'ina" paveiksliuką priklausomai nuo dabartinės būsenos.
 * T.Y. jei paveiksliukas jau "patinka" jis paspaudus jis "nebepatiks" ir vice-versa
 */
(function ($) {
    function LikeImage(){}

    LikeImage.prototype.options = {};

    LikeImage.prototype._create = function () {
        this.press = this.element.find('a');
        this._on(this.press, {
            "click": "start"
        });
        this.countField = this.element.find('span');
        this.lButton = this.element.find('img');
        this.Status = !!(this.element.attr('data-like_status') == "true");
        this.srcPath = this.lButton.attr("src").replace(/[^\/]*$/,"");
    };

    LikeImage.prototype.start = function () {
        if (this.inProgress)
            return false;

        $.ajax({
            url: this.press.attr('href'),
            type: "GET",
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

    LikeImage.prototype._initiate = function () {
        this.inProgress = true;
    };

    LikeImage.prototype._result = function (data) {
        if (data.success)
        {
            this.switch();
            this.countField.html(data.count);
        }
        else
        {
            $.globals.showStatus(0, data.message);
        }
        this.inProgress = false;
    };

    LikeImage.prototype._failure = function () {
        this.inProgress = false;
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
    };

    $.widget("custom.LikeImage", LikeImage.prototype);
})(jQuery);