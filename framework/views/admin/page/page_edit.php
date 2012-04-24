<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

?>
<script type="text/javascript">
require(['jquery-nos-ostabs'], function ($nos) {
	$nos(function () {
		$nos.nos.tabs.update({
			label : <?= json_encode($page->page_title) ?>,
			iconUrl : 'static/novius-os/admin/novius-os/img/16/page.png'
		});
	});
});
</script>

<?php
    echo View::forge('nos::layouts/languages',
        array(
            'item' => $page,
            'views' => array(
                'blank' => array(
                    'location' => 'nos::admin/page/page_form_blank_slate',
                    'params'   => array()
                ),
                'view' => array(
                    'location' => 'nos::admin/page/page_form',
                    'params'   => array('fieldset' => $fieldset)
                ),
            ),
        )
    , false);

