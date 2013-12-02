/*
 * Upload widget'as
 * Ajax'inis widgetas leidžiantis pridėti paveiksliuką
 * Rodomi ir procentai
 */
(function ($) {
    function Upload() {
    }

    Upload.prototype.options = {};

    Upload.prototype._create = function () {
        this.inProgress = false;
        this.pBarContainer = this.element.find('.meter');
        this.pBar = this.pBarContainer.find('span');
        this.sButton = this.element.find('#image_upload_Ikelti');
        this._updateProgress = $.globals.bind(this._updateProgress, this);
        /*
         *ajaxForm - http://malsup.com/jquery/form/, galima sužinoti įkelimo procentus
         *išsiunčiama nuspaudus <input type="submit">
         */
        this.send = $('#form_image_add').ajaxForm({
            context: this,
            beforeSend: this._initiate,
            uploadProgress: this._updateProgress,
            contentType: "application/json; charset=utf-8",
            dataType: 'json',
            error: function (xhr, ajaxOptions, thrownError) {
                $.globals.showStatus(0, 'Įkelti paveiksliuko nepavyko.');
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
        this.inProgress = true;
        this.sButton.prop("disabled", true);
        this.pBarContainer.show();
    };

    Upload.prototype._result = function (data) {
        this.pBar.width('100%');
        if (data.success && data.value !== undefined) {
            var fullimg = $('<div>',{html:data.value});
            fullimg.find('.delete-image').Delete({ aID: $.globals.container.attr('data-aID') });
            fullimg.find(".make_default").DefaultImage({ aID: $.globals.container.attr('data-aID') });
            $.globals.RefreshFancybox();
            $.globals.container.prepend(fullimg);
            fullimg.imagesLoaded(function(){
                $.globals.container.isotope( 'reloadItems' ).isotope({ sortBy: 'original-order' });
            });
        }
        $.globals.showStatus(data.success, data.message);
        this._hide();
        $.fancybox.close();

    };

    Upload.prototype._hide = function () {
        this.inProgress = false;
        this.sButton.prop("disabled", false);
        this.pBarContainer.hide(200);
    };
    $.widget("custom.Upload", Upload.prototype);
})(jQuery);