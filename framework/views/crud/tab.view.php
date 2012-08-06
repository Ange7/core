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
<div id="<?= $uniqid_close = uniqid('close_') ?>" style="display:none;">
    <p><?= $config['messages']['item deleted'] ?></p>
    <p>&nbsp;</p>
    <p><button class="primary" data-icon="close" onclick="$(this).nosTabs('close');"><?= __('Close tab') ?></button></p>
</div>
<script type="text/javascript">
	require(
        ['jquery-nos-ostabs'],
        function ($) {
            $(function () {
                var tabInfos = <?= \Format::forge()->to_json($tab_params) ?>,
                    isNew = <?= \Format::forge()->to_json($item->is_new()) ?>;

                var $container = $('#<?= $fieldset->form()->get_attribute('id') ?>');
                $container.nosTabs('update', tabInfos);
                if  (!isNew) {
                    $container.nosListenEvent({
                            name: <?= \Format::forge()->to_json($model) ?>,
                            action: 'delete',
                            id: '<?= $item->{$pk} ?>'
                        }, function() {
                            var $close = $('#<?= $uniqid_close ?>');
                            $close.show().nosFormUI();
                            $container.nosDialog({
                                title: <?= Format::forge()->to_json(__('Bye bye')) ?>,
                                content: $close,
                                width: 300,
                                height: 130,
                                close: function() {
                                    $container.nosTabs('close');
                                }
                            });
                        });
                }
            });
        });
</script>
