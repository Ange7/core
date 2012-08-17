/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

define(
    ['jquery-nos-appdesk'],
    function($) {
        "use strict";
        return function(appDesk) {
            return {
                appdesk : {
                    grid : {
                        columns : {
                            title : {
                                cellFormatter : function(args) {
                                    if ($.isPlainObject(args.row.data)) {
                                        var text = "";
                                        if (args.row.data.is_home) {
                                            text += ' <span class="ui-icon ui-icon-home" style="float:left;"></span> ';
                                        }
                                        text += args.row.data.title;

                                        args.$container.html(text);

                                        return true;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    });
