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

class Renderer_Virtualname extends \Fieldset_Field
{
    protected static $_friendly_slug_always_last = array();

    public static function _init()
    {
        \Config::load('friendly_slug', true);
        static::$_friendly_slug_always_last = \Config::get('friendly_slug.always_last');
    }

    public $template = '{label}{required} <div class="table-field">{field} <span>&nbsp;.html</span></div> {use_title_checkbox}';

    public function build()
    {
        parent::build();

        $this->apply_use_title_checkbox();

        $this->fieldset()->append($this->js_init());

        if ($this->fieldset()->getInstance()->is_new()) {
            $this->set_attribute('data-usetitle', 1);
        }

        return (string) parent::build();
    }

    public function apply_use_title_checkbox()
    {
        $use_title_checkbox = \View::forge('renderer/virtualname/use_title_checkbox', array(
            'id' => $this->get_attribute('id'),
        ), false);
        $this->template = str_replace('{use_title_checkbox}', $use_title_checkbox, $this->template);

    }

    public function js_init()
    {
        $default = \Config::get('friendly_slug.active_setup', 'default');
        $options = array(
            \Config::get('friendly_slug.setups.'.$default, array())
        );
        $this->fieldset()->getInstance()->event('friendlySlug', array(&$options));
        $options[] = static::$_friendly_slug_always_last;

        return \View::forge('renderer/virtualname/js', array(
            'id' => $this->get_attribute('id'),
            'options' => $options,
        ), false);
    }

}
