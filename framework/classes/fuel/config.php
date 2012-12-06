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
 * Extended Config class to allow callback on the loaded config
 */
class Config extends \Fuel\Core\Config
{
    public static function load($file, $group = null, $reload = false, $overwrite = false)
    {
        if ($file == 'db') {
            $group = 'db';
        }
        if (!$reload and is_array($file) and is_string($group)) {
            Event::trigger_function('config|'.$group, array(&$file));
        }

        return parent::load($file, $group, $reload, $overwrite);
    }

    public static function mergeWithUser($item, $config)
    {
        $user = Session::user();

        Arr::set($config, 'configuration_id', static::getDbName($item));

        return \Arr::merge($config, \Arr::get($user->getConfiguration(), static::getDbName($item), array()));
    }

    public static function configFile($class)
    {
        $namespace = trim(\Inflector::get_namespace($class), '\\');

        $application = mb_strtolower($namespace);
        $file = mb_strtolower(str_replace('_', DS, \Inflector::denamespace($class)));

        if ($application !== 'nos') {
            \Config::load(APPPATH.'metadata/app_namespaces.php', 'data::app_namespaces');
            $namespaces = Config::get('data::app_namespaces', null);
            if ($app = array_search($namespace, $namespaces)) {
                $application = $app;
            }
        }

        return array($application, $file);
    }

    public static function loadConfiguration($app_name, $file_name)
    {
        \Config::load($app_name.'::'.$file_name, true);
        $config = \Config::get($app_name.'::'.$file_name);
        \Config::load(APPPATH.'metadata'.DS.'app_dependencies.php', 'data::app_dependencies');
        $dependencies = \Config::get('data::app_dependencies', array());

        if (!empty($dependencies[$app_name])) {
            foreach ($dependencies[$app_name] as $dependency) {
                \Config::load($dependency.'::'.$file_name, true);
                $config = \Arr::merge($config, \Config::get($dependency.'::'.$file_name));
            }
        }
        $config = \Arr::recursive_filter(
            $config,
            function($var) {
                return $var !== null;
            }
        );

        return $config;
    }

    public static function getDbName($item)
    {
        $item = str_replace('::', '/config/', $item);
        $item = str_replace('/', '.', $item);

        return $item;
    }

    public static function extendable_load($module_name, $file_name)
    {
        \Config::load($module_name.'::'.$file_name, true);
        $config = \Config::get($module_name.'::'.$file_name);
        \Config::load(APPPATH.'metadata'.DS.'app_dependencies.php', 'data::app_dependencies');
        $dependencies = \Config::get('data::app_dependencies', array());

        if (!empty($dependencies[$module_name])) {
            foreach ($dependencies[$module_name] as $dependency) {
                \Config::load($dependency.'::'.$file_name, true);
                $config = \Arr::merge($config, \Config::get($dependency.'::'.$file_name, array()));
            }
        }
        $config = \Arr::recursive_filter(
            $config,
            function($var) {
                return $var !== null;
            }
        );

        return $config;
    }

    public static function metadata($application_name)
    {
        \Config::load($application_name.'::metadata', true);
        return \Config::get($application_name.'::metadata');
    }

    public static function application($application_name)
    {
        return static::extendable_load($application_name, 'config');
    }

    public static function actions($params = array())
    {
        if (!isset($params['models'])) {
            return array();
        }

        $selected_actions = array();
        foreach ($params['models'] as $model) {
            $common_config = \Nos\Config_Common::load($model, array());
            $actions = $common_config['actions']['list'];
            $actions_order = $common_config['actions']['order'];

            $params['model'] = $model;

            if (!empty($params['item']) && isset($action['enabled'])) {
                $action['enabled'] = $action['enabled']($params['item']);
            }

            foreach ($actions_order as $key) {
                $action = $actions[$key];
                if (static::can_add_action($action, $params)) {
                    $selected_actions[$key] = $action;
                }
                unset($actions[$key]);
            }

            foreach ($actions as $key => $action) {
                if (static::can_add_action($action, $params)) {
                    $selected_actions[$key] = $action;
                }
            }
        }

        return $selected_actions;
    }

    protected static function can_add_action($action, $params)
    {
        if (isset($action['targets']) && (!isset($action['targets'][$params['target']]) || !$action['targets'][$params['target']])) {
            return false;
        }

        return !isset($action['visible']) || $action['visible']($params);
    }

    public static function placeholderReplace($obj, $data)
    {
        $retrieveFromData = function($arg, $data) {
            if (isset($data[$arg])) {
                return $data[$arg];
            }
            if (isset($data->{$arg})) {
                return $data->{$arg};
            }
            try {
                return $data->{$arg}();
            } catch (\Exception $e) {
                return '';
            }
        };

        if (is_string($obj)) {
            $obj = preg_replace_callback('/\[\:([\w]+)\]/', function($matches) use($data, $retrieveFromData) {
                return isset($matches[1]) ? $retrieveFromData($matches[1], $data) : '';
            }, $obj);
            $obj = preg_replace_callback('/{{([\w]+)}}/', function($matches) use($data, $retrieveFromData) {
                return isset($matches[1]) ? $retrieveFromData($matches[1], $data) : '';
            }, $obj);
            $obj = preg_replace_callback('/{{urlencode:([\w]+)}}/', function($matches) use($data, $retrieveFromData) {
                return urlencode(isset($matches[1]) ? $retrieveFromData($matches[1], $data) : '');
            }, $obj);
        } else if (is_array($obj)) {
            foreach ($obj as $key => $value) {
                $new_key = static::placeholderReplace($key, $data);
                $obj[$new_key] = static::placeholderReplace($value, $data);
                if ($new_key !== $key) {
                    unset($obj[$key]);
                }
            }
        }
        return $obj;
    }

    public static function icon($application_name, $icon_key)
    {
        \Config::load($application_name.'::metadata', true);
        $metadata = \Config::get($application_name.'::metadata');
        return $metadata['icons'][$icon_key];
    }

}

/* End of file config.php */
