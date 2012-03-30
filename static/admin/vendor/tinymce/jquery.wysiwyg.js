define([
    'jquery',
    'tinymce'
], function($) {
    var inc = 1;
    $.fn.wysiwyg = function(options) {

		var self = $(this);
		$.ajax({
			dataType: 'json',
			url: 'admin/nos/wysiwyg/enhancers',
			success: function(enhancers) {
				options = $.extend({
					// Location of TinyMCE script
					script_url : '/static/novius-os/admin/vendor/tinymce/tiny_mce.js',
					theme      : 'nos',
					plugins    : 'spellchecker,xhtmlxtras,style,table,advlist,inlinepopups,media,searchreplace,paste,noneditable,visualchars,nonbreaking',
					theme_nos_enhancers : enhancers
				}, options || {});

				$(self).tinymce(options);

			}
		});
    };
    return $;
});