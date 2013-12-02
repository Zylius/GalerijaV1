/*
 * PostComment widget'as
 * Ajax'inis widgetas leidžiantis pridėti komentarą
 */
(function ($) {
    function PostComment() {}

    PostComment.prototype.options = {};

    PostComment.prototype._create = function () {
        this.inProgress = false;
        this.sButton = this.element.find('#comment_post_Ikelti');
        this.send = this.element.ajaxForm({
            context: this,
            beforeSend: this._initiate,
            error: function (xhr, ajaxOptions, thrownError) {
                $.globals.showStatus(0, 'Komentuoti nepavyko.');
                this._hide();
            },
            success: this._result
        });
    };


    PostComment.prototype._initiate = function () {
        this.sButton.prop("disabled", true);
        this.inProgress = true;
    };

    PostComment.prototype._result = function (data) {
        if (data.success) {
            var comment = $('<li><div class="control"><img class="disappear delete-comment" data-id="' + data.id + '"' +
                'alt="Delete" src="' + data.delpath + '" data-csrf_token="'+ data.token +'"/></div>' +
                '<fieldset><legend>'+ data.username + '</legend>'+ data.value + '</fieldset></li>');
            $(".comments").find("ul").append(comment);
            comment.find('img').Delete({
                type: 'comment',
                route: 'galerija_comment_delete'
            });
        }
        $.globals.showStatus(data.success, data.message);
        this._hide();
    };
    PostComment.prototype._hide = function () {
        this.inProgress = false;
        this.sButton.prop("disabled", false);
    };

    $.widget("custom.PostComment", PostComment.prototype);
})(jQuery);