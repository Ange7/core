<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

$uniqid = uniqid(str_replace(':', '_', $context).'_');

$labels = array();
$possible = $item->get_possible_context();
$main_context = $item->find_main_context();
$common_id = $main_context ? $main_context->id : false;

$view_params['container_id'] = $uniqid;

echo View::forge('nos::crud/tab', $view_params, false);

?>
<div id="<?= $uniqid ?>" class="" style="padding:0;">
    <div class="blank_slate">
<?php
if (!in_array($context, $possible)) {
    echo '<p>&nbsp;</p>';
    $parent = $crud['behaviours']['tree'] ? $item->get_parent() : null;
    if (!empty($parent)) {
        $uniqid_parent = uniqid('parent_');
        echo strtr(__('error added in context not parent'), array(
            '{context}' => \Nos\Tools_Context::contextLabel($context),
            '{parent}' => '<a href="javascript:void;" id="'.$uniqid_parent.'">'.__('parent').'</a>',
        ));
        ?>
        <script type="text/javascript">
            require(['jquery-nos'], function($nos) {
               $nos('#<?= $uniqid_parent ?>').click(function() {
                   $nos(this).tab('open', <?= \Format::forge()->to_json(array('url' => $url_insert_update.'/'.$parent->id.'?context='.$context)) ?>);
               });
            });
        </script>
        <?php
    } else {
        echo strtr(__('error added in context'), array('{context}' => \Nos\Tools_Context::contextLabel($context)));
    }
} else {
    foreach ($possible as $possible_context) {
        $item_context = $item->find_context($possible_context);
        if (!empty($item_context)) {
            $labels[$item_context->id] = \Nos\Tools_Context::contextLabel($possible_context, array('template' => '{site} - {locale}', 'flag' => false));
        }
    }
    $locale_item = \Nos\Tools_Context::localeCode($item->get_context());
    $locale_new = \Nos\Tools_Context::localeCode($context);

    if ($locale_item === $locale_new) {
        $label = __('Add "{item}" to {context}');
    } else {
        $label = __('Translate "{item}" into {context}');
    }
    echo '<h1>', strtr($label, array('{item}' => $item->title_item(), '{context}' => \Nos\Tools_Context::contextLabel($context))), '</h1>';
    ?>
            <p>&nbsp;</p>

            <p><?= __('You have two options: ') ?></p>

            <p>&nbsp;</p>

            <ul style="margin-left:1em;">
                <li>
                    <span style="display:inline-block; width:2em;"></span>
                    <form action="<?= $crud['url_form'] ?>" style="display:inline-block;">
                        <?= Form::hidden('context', $context) ?>
                        <?= Form::hidden('common_id', $common_id) ?>
                        <button type="submit" class="primary" data-icon="plus"><?= __('Start from scratch ') ?></button>
                    </form>
                    <p style="font-style: italic; padding: 5px 0 2em 4em;"><?= __('(Blank form)') ?></p>
                </li>
                <li>
                    <span class="faded" style="display:inline-block; width:2em;"><?= __('OR') ?></span>
                    <form action="<?= $crud['url_form'] ?>" style="display:inline-block;">
                        <?= Form::hidden('context', $context) ?>
                        <?= Form::hidden('common_id', $common_id) ?>
    <?php
    if (count($labels) == 1) {
        echo Form::hidden('create_from_id', key($labels));
        $selected_context = current($labels);
    } else {
        $selected_context = Form::select('create_from_id', null, $labels);
    }

    $button = '<button type="submit" class="primary" data-icon="plus">'.($locale_item === $locale_new ? __('Copy') : __('Translate')).'</button>';
    echo strtr(__('{translate} the {context} version'), array(
        '{translate}' => $button,
        '{context}' => $selected_context,
    ));
    ?>
                    </form>
                    <p style="font-style: italic; padding: 5px 0 2em 4em;"><?= __('(Form filled with the content from the original version)') ?></p>
                </li>
            </ul>
    <?php
}
?>
    </div>
</div>
<script type="text/javascript">
require(['jquery-nos'], function ($) {
    $(function () {
        var $container = $('#<?= $uniqid ?>').nosFormUI();
        $container.find('form').submit(function(e) {
            e.preventDefault();
            var $form = $(this);
            $container.load($form.get(0).action, $form.serialize());
        });
    });
});
</script>
