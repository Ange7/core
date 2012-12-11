<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */


Nos\I18n::current_dictionary(array('noviusos_media::common', 'nos::common'));

$uniqid_radio = uniqid('radio_');
?>
<div class="page" id="<?= $uniqid = uniqid('id_') ?>">
<?php
$basename = pathinfo($item->medif_path, PATHINFO_BASENAME);
$checked = $basename === \Nos\Orm_Behaviour_Virtualname::friendly_slug($item->medif_title);

$fieldset->set_config('field_template', '{field}');

foreach ($fieldset->field() as $field) {
    if ($field->type == 'submit') {
        $field->set_template('{field}');
    }
    if ($field->type == 'file') {
        $form_attributes = $fieldset->get_config('form_attributes', array());
        $form_attributes['enctype'] = 'multipart/form-data';
        $fieldset->set_config('form_attributes', $form_attributes);
    }
}
if (!$item->is_new()) {
    echo $fieldset->field('medif_id')->build();
}
?>
    <table class="fieldset">
        <tr>
            <th><?= $fieldset->field('medif_title')->label; ?></th>
            <td><?= $fieldset->field('medif_title')->build(); ?></td>
        </tr>
        <tr style="height:85px;">
            <th style="vertical-align: top;"><?= $fieldset->field('medif_dir_name')->label; ?></th>
            <td style="width:350px;vertical-align: top;">
                <label><input type="checkbox" data-id="same_title" <?= $checked ? 'checked' : '' ?>> <?= __('Generate from title') ?></label> <br />
                <span style="vertical-align:middle;">
                    http://yoursite.com/media/<span data-id="path_prefix"><?= $item->is_new() ? (!empty($crud['environment']) ? $crud['environment']->medif_path : '') : $item->parent->medif_path ?></span>
                </span>
                <?= $fieldset->field('medif_dir_name')->build(); ?>
            </td>
        </tr>
<?php
if ($item->is_new()) {
    ?>
        <tr>
            <th><?= !empty($crud['environment']) ? '' :  $fieldset->field('medif_parent_id')->label; ?></th>
            <td id="<?= $uniqid_radio ?>"><?= $fieldset->field('medif_parent_id')->build(); ?></td>
        </tr>
    <?php
}
?>
    </table>
</div>

<script type="text/javascript">
    require(
        ['jquery-nos-media-folder-form'],
        function($) {
            $(function() {
                $('#<?= $uniqid ?>').nosMediaFolderForm({
                    containerParentId: '#<?= $uniqid_radio ?>'
                });
            });
        });
</script>
