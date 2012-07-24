/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

define(
    [
        'jquery-nos-wysiwyg',
        'jquery-nos'
    ],
    function($) {
        "use strict";
        return function() {

            var $container = $(this);
            if ($container.data('already-processed')) {
                return;
            }

            $container.find('input[name=page_meta_noindex]').change(function() {
                $(this).closest('p').nextAll()[$(this).is(':checked') ? 'hide' : 'show']();
            }).change();


            $container.find('input[name=page_menu]').change(function(e) {
                if ($(this).is(':disabled')) {
                    e.preventDefault();
                    e.stopPropagation();
                    return;
                }
                var $siblings = $(this).closest('p').nextAll();
                if ($(this).is(':checked')) {
                    $siblings.show();
                } else {
                    $siblings.hide();
                }
            }).change();

            var $page_id = $container.find('input[name=page_id]');
            var $from_id = $container.find('input[name=create_from_id]');
            var from_id = $page_id.val() || $from_id.val() || 0;
            $container.find('select[name=page_template]').bind('change', function() {
                $container.data('already-processed', true);
                var $wysiwyg = $container.find('[data-id=wysiwyg]');
                $.ajax({
                    url: 'admin/nos/page/ajax/wysiwyg/' + from_id,
                    data: {
                        template_id: $(this).val()
                    },
                    dataType: 'json',
                    success: function(data) {

                        $wysiwyg.nosOnShow('one', function() {
                            var ratio = $wysiwyg.width() * 3 / 5;
                            $wysiwyg.empty().css({
                                height: ratio,
                                overflow: 'visible'
                            });
                            $.each(data.layout, function(i) {
                                var coords = this.split(',');
                                var bloc = $('<div></div>').css({
                                    position: 'absolute',
                                    left:   Math.round(coords[0] / data.cols * 100) + '%',
                                    top:    Math.round(coords[1] / data.rows * ratio),
                                    width:  Math.round(coords[2] / data.cols * 100) + '%',
                                    height: Math.round(coords[3] / data.rows * ratio)
                                }).append(
                                    $('<textarea></textarea>')
                                    .val(data.content[i])
                                    .attr({name: 'wysiwyg[' + i + ']'})
                                    .addClass('wysiwyg')
                                    .css({
                                        display: 'block',
                                        width: '100%',
                                        height: Math.round(coords[3] / data.rows * ratio),
                                        border: 0,
                                        boxShadow: 'inset 0px 0px 2px 2px  #888'
                                    }));
                                $wysiwyg.append(bloc);
                                // The bottom row from TinyMCE is roughly 21px
                                $wysiwyg.find('[name="wysiwyg[' + i + ']"]').wysiwyg({
                                    urlEnhancers : true,
                                    height: (coords[3] / data.rows * ratio) - 21,
                                    content_css: data.content_css || ''
                                });
                            });
                        });
                    }
                })
            });

            var $template_unit = $container.find('select[name=page_template]').closest('td');
            $container.find('select[name=page_type]').change(function() {
                var val = $(this).val();
                var $wysiwyg = $container.find('[data-id=wysiwyg]');
                var $external = $container.find('[data-id=external]');
                var $internal = $container.find('[data-id=internal]');

                // 0 = Classic
                // 2 = Folder
                // 3 = External link
                // 4 = Internal link

                if (val == 0 || val == 2) {
                    $wysiwyg.show().siblings().hide();
                    $template_unit.show().end().change();
                }

                if (val == 3) {
                    $external.show().siblings().hide();
                    $template_unit.hide();
                }

                if (val == 4) {
                    $internal.show().siblings().hide();
                    $template_unit.hide();
                }
            }).change();

            var $title      = $container.find('input[name=page_title]');
            var $menu_title = $container.find('input[name=page_menu_title]');
            var $checkbox_menu   = $container.find('input[data-id=same_menu_title]');
            $title.bind('change keyup', function() {
                if ($checkbox_menu.is(':checked')) {
                    $menu_title.val($title.val());
                }
            });
            if ($title.val() == $menu_title.val() || $menu_title.val() == '') {
                $checkbox_menu.attr('checked', true).wijcheckbox("refresh");
            }
            $checkbox_menu.change(function() {
                if ($(this).is(':checked')) {
                    $menu_title.attr('readonly', true).addClass('ui-state-disabled').removeClass('ui-state-default');
                    $title.triggerHandler('change');
                } else {
                    $menu_title.removeAttr('readonly').addClass('ui-state-default').removeClass('ui-state-disabled');
                }
            }).triggerHandler('change');

            var replace_url = function(str) {
                if (!str) {
                    return str;
                }
                return str.replace(/_/g, '-')
                    .replace(/ /g, '-')
                    .replace(/:/g, '-')
                    .replace(/\\/g, '-')
                    .replace(/\//g, '-')
                    .replace(/[^a-zA-Z0-9\-]+/g, '')
                    .replace(/-{2,}/g, '-')
                    .toLowerCase();
            };

            var $virtual_name  = $container.find('input[name=page_virtual_name]');
            var $checkbox_url = $container.find('input[data-id=same_url_title]');
            $title.bind('change keyup', function() {
                if ($checkbox_url.is(':checked')) {
                    $virtual_name.val(replace_url($title.val()));
                }
            });
            if (replace_url($title.val()) == $virtual_name.val() || $virtual_name.val() == '') {
                $checkbox_url.attr('checked', true).wijcheckbox("refresh");
            }
            $checkbox_url.change(function() {
                if ($(this).is(':checked')) {
                    $virtual_name.attr('readonly', true).addClass('ui-state-disabled').removeClass('ui-state-default');
                    $title.triggerHandler('change');
                } else {
                    $virtual_name.removeAttr('readonly').addClass('ui-state-default').removeClass('ui-state-disabled');
                }
            }).triggerHandler('change');
        }
    });