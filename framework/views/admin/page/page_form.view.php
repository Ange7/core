<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

echo View::forge('nos::form/crud', array('crud' => $crud), false);
?>
<script type="text/javascript">
    require(['jquery-nos', 'static/novius-os/admin/config/page/form.js'], function ($, callback_fn) {
        $(function () {
            callback_fn.call($('#<?= $crud['fieldset']->form()->get_attribute('id') ?>'));
        });
    });
</script>