<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */
echo $view_params['fieldset']->open($view_params['url_insert_update']);
echo View::forge('nos::crud/tab', array(
        'model' => $view_params['model'],
        'pk' => $view_params['pk'],
        'item' => $view_params['item'],
        'config' => $view_params['config'],
        'container_id' => $view_params['fieldset']->form()->get_attribute('id'),
        'tab_params' => $view_params['tab_params'],
    ), false);
echo View::forge('nos::crud/toolbar', array(
        'container_id' => $view_params['fieldset']->form()->get_attribute('id'),
        'config' => $view_params['config'],
        'actions' => $view_params['actions'],
        'fieldset' => $view_params['fieldset'],
        'item' => $view_params['item'],
    ), false);

$layout = $view_params['is_new'] ? $view_params['config']['layout_insert'] : $view_params['config']['layout_update'];
$view = current($layout);
if (!is_array($view) || empty($view['view'])) {
    $layout = array(
        array(
            'view' => 'nos::form/layout_standard',
            'view_params' =>  $layout,
        ),
    );
}
foreach ($layout as $view) {
    if (!empty($view['view'])) {
        $view['view_params'] = empty($view['view_params']) ? array() : $view['view_params'];
        echo View::forge($view['view'], $view['view_params'] + array(
            'fieldset' => $view_params['fieldset'],
            'item' => $view_params['item'],
            'context' => $view_params['context'],
        ), false);
    }
}

echo $view_params['fieldset']->close();
