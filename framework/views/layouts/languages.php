<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

/**
 * Layout for language choice... On page and blog applications.
 *
 * @param item: should be an object
 *
 * @param views: array containing views informations
 *          - blank: blank view
 *              - location
 *              - params: parameters of the view
 *          - view: main view
 *              -location
 *              - params
 */

?>

<?php
    $uniqids = array();
    $locales = Config::get('locales', array());
    $possible = $item->get_possible_lang();
    foreach ($possible as $lang) {
        $uniqids[$lang] = uniqid($lang.'_');
    }
?>
<div id="<?= $uniqid_tabs = uniqid('tabs_') ?>" class="nos-tabs line ui-widget fill-parent" style="clear:both; margin:0;display:none;">
    <ul class="nos-tabs-lang-header">
        <?php
        foreach ($possible as $lang) {
            echo '<li style="text-align: center;"><a href="#'.$uniqids[$lang].'">'.Nos\Helper::flag($lang).'</a></li>';
        }
        ?>
    </ul>
<?php
    $labels     = array();
    $items_lang = array();

    //\Debug::dump($item->find_main_lang());
    $main_lang = $item->find_main_lang();
    $common_id = $main_lang ? $main_lang->id : false;
    foreach ($possible as $lang) {
        $items_lang[$lang] = $item->find_lang($lang);
        if (!empty($items_lang[$lang])) {
            $labels[$items_lang[$lang]->id] = Arr::get($locales, $lang, $lang);
        }
    }
    foreach ($possible as $lang) {
        $item_lang = $items_lang[$lang];
        ?>
        <div id="<?= $uniqids[$lang] ?>" class="page_lang fill-parent" style="padding:0;">
            <?php
            if (empty($item_lang)) {
                echo View::forge($views['blank']['location'],
                array_merge(array(
                    'lang'      => $lang,
                    'common_id' => $common_id,
                    'possible'  => $labels,
                ), $views['blank']['params']), false);
            } else {
                echo View::forge($views['view']['location'],
                    array_merge(array(
                        'lang'      => $lang,
                        'uniqid'    => $uniqids[$lang],
                        /*'fieldset' => $fieldset,*/
                        'item'      => $item_lang
                        ),
                        $views['view']['params']
                ), false);
            }
            ?>
        </div>
        <?php
    }
    ?>
</div>
<script type="text/javascript">
	require([
		'jquery-nos',
		'static/novius-os/admin/config/page/form.js'
	], function($nos, callback_fn) {
		$nos(function() {
			var $tabs = $nos('#<?= $uniqid_tabs ?>');
            $tabs.initOnShow('init', function() {
                $tabs.wijtabs({
                    alignment: 'left',
                    show: function(e, ui) {
                        $nos(ui.panel).initOnShow().trigger('blank_slate');
                    }
                });
                $tabs.find('> div').addClass('fill-parent').addClass('nos-tabs-lang-content');
                $tabs.find('.page_lang').one('blank_slate', callback_fn)
                $tabs.wijtabs('select', '#<?= $uniqids[$item->get_lang()] ?>');
            });
            $tabs.initOnShow();
        });
    });
/*
    $nos(function() {
        return;
        var $tabs = $nos('#<?= $uniqid_tabs ?>');
        $tabs.initOnShow('init', function() {
            $tabs.find('div.page_lang').one('blank_slate', callback_fn);
            $tabs.wijtabs({
                alignment: 'left',
                show: function(e, ui) {
                    //$nos(ui.panel).initOnShow();
                    $nos(ui.panel).initOnShow().trigger('blank_slate');
                }
            });
            $tabs.find('> div').addClass('fill-parent').addClass('nos-tabs-lang-content');
            $tabs.find('div.page_lang').initOnShow();
            $tabs.wijtabs('select', '#<?= $uniqids[$item->get_lang()] ?>');
        });
        $tabs.initOnShow();
    });*/
</script>
