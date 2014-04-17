<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2014 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Nos\Menu;

/**
 * Abstract class for a menu item driver
 *
 * @package Nos\Menu
 */
abstract class Menu_Item_Driver
{
    // The menu item
    public $item = null;

    protected static $config_cached = array();

    /**
     * Constructor
     *
     * @param Model_Menu_Item $item
     * @return Menu_Item_Driver $this
     */
    public function __construct(Model_Menu_Item $item)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * @return string The item title
     */
    public function title()
    {
        return $this->item->mitem_title;
    }

    /**
     * Get the driver configuration
     *
     * @param bool $reload
     * @return array
     */
    public static function config($reload = false)
    {
        $class = get_called_class();
        if (!isset(static::$config_cached[$class]) || $reload) {
            list($application_name, $config_file) = \Config::configFile($class);
            static::$config_cached[$class] = \Arr::merge(
                \Config::load('noviusos_menu::menu/item/driver', true),
                \Config::load($application_name.'::'.$config_file)
            );
        }

        return static::$config_cached[$class];
    }
}
