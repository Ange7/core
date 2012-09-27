<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Nos;

class Widget_Page_Selector extends \Nos\Widget_Selector
{
    public function before_construct(&$attributes, &$rules)
    {
        $attributes['class'] = (isset($attributes['class']) ? $attributes['class'] : '').' nos-page';

        if (empty($attributes['id'])) {
            $attributes['id'] = uniqid('page_');
        }
    }

    public function build()
    {
        return $this->template(static::widget(array(
            'input_name' => $this->name,
            'selected' => array(
                // Converts null to 0
                'id' => (string) (int) $this->value,
            ),
            'treeOptions' => array(
                'context' => \Arr::get($this->widget_options, 'context', null),
            ),
            'height' => \Arr::get($this->widget_options, 'height', '150px'),
            'width' => \Arr::get($this->widget_options, 'width', null),
        )));
    }

    /**
     * Returns a page selector widget
     * @static
     * @param array $options
     */
    public static function widget($options = array())
    {
        $options = \Arr::merge(array(
            'treeUrl' => 'admin/nos/page/inspector/page/json',
            'reloadEvent' => 'Nos\\Model_Page',
            'input_name' => null,
            'selected' => array(
                'id' => null,
                'model' => 'Nos\\Model_Page',
            ),
            'columns' => array(
                array(
                    'dataKey' => 'title',
                )
            ),
            'treeOptions' => array(
                'context' => null
            ),
            'height' => '150px',
            'width' => null,
        ), $options);

        return (string) \Request::forge('nos/admin/page/inspector/page/list')->execute(
            array(
                'inspector/modeltree_radio',
                array(
                    'params' => $options,
                )
            )
        )->response();
    }
}
