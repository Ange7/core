<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

// Get the start time and memory for use later
defined('FUEL_START_TIME') or define('FUEL_START_TIME', microtime(true));
defined('FUEL_START_MEM') or define('FUEL_START_MEM', memory_get_usage());

// Setup dir constants
if (isset($_SERVER['NOS_ROOT'])) {
    define('NOS_ROOT', $_SERVER['NOS_ROOT'].DIRECTORY_SEPARATOR);
} else {
    define('NOS_ROOT', realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR);
}
define('DOCROOT', NOS_ROOT.'public'.DIRECTORY_SEPARATOR);

define('APPPATH', NOS_ROOT.'local'.DIRECTORY_SEPARATOR);
define('PKGPATH', NOS_ROOT.'novius-os'.DIRECTORY_SEPARATOR.'packages'.DIRECTORY_SEPARATOR);
define('COREPATH', NOS_ROOT.'novius-os'.DIRECTORY_SEPARATOR.'fuel-core'.DIRECTORY_SEPARATOR);
define('NOSPATH', NOS_ROOT.'novius-os'.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR);

define('FUEL_EXTEND_PATH', NOSPATH.'classes'.DIRECTORY_SEPARATOR.'fuel'.DIRECTORY_SEPARATOR);

// Load in the Autoloader
require FUEL_EXTEND_PATH.'autoloader.php';
//class_alias('Fuel\\Core\\Autoloader', 'Autoloader');

// Bootstrap the framework DO NOT edit this
require_once COREPATH.'bootstrap.php';
if (!MBSTRING) {
    require_once NOSPATH.'mb_string.php';
}

\Autoloader::add_classes(
    array(
        // Add classes you want to override here
        // Example: 'View' => APPPATH.'classes/view.php',
        'Arr' => FUEL_EXTEND_PATH.'arr.php',
        'Command' => FUEL_EXTEND_PATH.'oil'.DIRECTORY_SEPARATOR.'command.php',
        'Config' => FUEL_EXTEND_PATH.'config.php',
        'Config_File' => FUEL_EXTEND_PATH.'config_file.php',
        'Date' => FUEL_EXTEND_PATH.'date.php',
        'Debug' => FUEL_EXTEND_PATH.'debug.php',
        'Event_Instance' => FUEL_EXTEND_PATH.'event/instance.php',
        'Fuel' => FUEL_EXTEND_PATH.'fuel.php',
        'Finder' => FUEL_EXTEND_PATH.'finder.php',
        'Fieldset' => FUEL_EXTEND_PATH.'fieldset.php',
        'Fieldset_Field' => FUEL_EXTEND_PATH.'fieldset_field.php',
        'Generate' => FUEL_EXTEND_PATH.'oil'.DIRECTORY_SEPARATOR.'generate.php',
        'Migrate' => FUEL_EXTEND_PATH.'migrate.php',
        'Module' => FUEL_EXTEND_PATH.'module.php',
        'ModuleNotFoundException' => FUEL_EXTEND_PATH.'module.php',
        'Profiler' => FUEL_EXTEND_PATH.'profiler.php',
        'Refine' => FUEL_EXTEND_PATH.'oil'.DIRECTORY_SEPARATOR.'refine.php',
        'Response' => FUEL_EXTEND_PATH.'response.php',
        'Session' => FUEL_EXTEND_PATH.'session.php',
        'Str' => FUEL_EXTEND_PATH.'str.php',
        'View' => FUEL_EXTEND_PATH.'view.php',
        'Nos\Oil\Console' => FUEL_EXTEND_PATH.'oil'.DIRECTORY_SEPARATOR.'console.php',
        'Nos\Orm\Model' => FUEL_EXTEND_PATH.'orm'.DIRECTORY_SEPARATOR.'model.php',
        'Nos\Orm\Query' => FUEL_EXTEND_PATH.'orm'.DIRECTORY_SEPARATOR.'query.php',
        'Nos\Tasks\Migrate' => FUEL_EXTEND_PATH.'tasks'.DIRECTORY_SEPARATOR.'migrate.php',
    )
);

function __($_message, $default = null)
{
    return \Nos\I18n::get($_message, $default);
}

// Register the autoloader
Autoloader::register();

/**
 * Your environment.  Can be set to any of the following:
 *
 * Fuel::DEVELOPMENT
 * Fuel::TEST
 * Fuel::STAGE
 * Fuel::PRODUCTION
 */
Fuel::$env = (isset($_SERVER['NOS_ENV']) ? $_SERVER['NOS_ENV'] : (isset($_SERVER['FUEL_ENV']) ? $_SERVER['FUEL_ENV'] : Fuel::DEVELOPMENT));

//* Register application autoloader
spl_autoload_register(
    function ($class) {

        $class = ltrim($class, '\\');
        $parts = explode('\\', $class);
        $class = array_pop($parts);
        $namespace = implode('\\', $parts);

        // We can't load classes inside those namespaces
        if ($namespace == '' || $namespace == 'Fuel\\Core') {
            return false;
        }

        // Computes this after the above lines, as the Inflector class will be autoloaded
        $namespace_upper_words = Inflector::words_to_upper($namespace);

        // An application can be put inside any namespace when properly configured
        if (!empty(Fuel::$namespace_aliases[$namespace_upper_words])) {
            if (class_exists(Fuel::$namespace_aliases[$namespace_upper_words].'\\'.$class)) {
                class_alias(Fuel::$namespace_aliases[$namespace_upper_words].'\\'.$class, $namespace.'\\'.$class);
            }
            if (class_exists($namespace.'\\'.$class, false)) {
                return true;
            }
        }

        // Try to load the application
        \Config::load(APPPATH.'metadata/app_namespaces.php', 'data::app_namespaces');
        $namespaces = \Config::get('data::app_namespaces');
        $application = array_search($namespace, $namespaces);
        if (false !== $application) {
            \Module::load($application);
        }

        return false;
    },
    true,
    true
);
//*/

// Initialize the framework with the config file.
$config_novius = include(NOSPATH.'config/config.php');

$routes_novius = @include(NOSPATH.'config/routes.config.php');
if ($routes_novius === false) {
    $routes_novius = include(NOSPATH.'config/routes.php');
}

$config_app = include(APPPATH.'config/config.php');
if (!empty($config_app['base_url'])) {
    define('NOS_RELATIVE_DIR', ltrim(parse_url($config_app['base_url'], PHP_URL_PATH), '/'));
}

Fuel::init(Arr::merge($config_novius, array('routes' => $routes_novius), $config_app));

Module::load('nos', NOSPATH);

define('URL_ADMIN', Uri::base(false).'admin/');
define('PHP_BEGIN', '<?php ');
define('PHP_END', ' ?>');
Module::load('local', APPPATH);

//Autoloader::add_namespace('Nos', NOSPATH.'classes'.DS);
//Autoloader::add_namespace('Local', APPPATH.'classes'.DS);

Config::load('namespaces', true);

foreach (Config::get('namespaces', array()) as $ns => $path) {
    Autoloader::add_namespace($ns, APPPATH.'..'.DS.$path.'classes'.DS);
}

chdir(DOCROOT);

define('CACHE_DURATION_PAGE', 5);
define('CACHE_DURATION_FUNCTION', 10);
