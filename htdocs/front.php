<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

define('DOCROOT', realpath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR);

define('APPPATH',  realpath(DOCROOT.'../local/').DIRECTORY_SEPARATOR);
define('PKGPATH',  realpath(DOCROOT.'../novius-os/packages/').DIRECTORY_SEPARATOR);
define('COREPATH', realpath(DOCROOT.'../novius-os/fuel-core/').DIRECTORY_SEPARATOR);
define('NOSPATH',  realpath(DOCROOT.'../novius-os/framework/').DIRECTORY_SEPARATOR);

// Get the start time and memory for use later
defined('FUEL_START_TIME') or define('FUEL_START_TIME', microtime(true));
defined('FUEL_START_MEM') or define('FUEL_START_MEM', memory_get_usage());

// Boot the app
require_once NOSPATH.'bootstrap.php';

if (empty($_SERVER['REDIRECT_URL']) && !empty($_GET['URL'])) {
	$_SERVER['REDIRECT_URL'] = $_GET['URL'].'.html';
	if ($_SERVER['REDIRECT_URL'] == '/index.html')
	{
		$_SERVER['REDIRECT_URL'] = '/';
	}
}

// Generate the request, execute it and send the output.
$response = Request::forge('nos/front/index', false)->execute()->response();

// This will add the execution time and memory usage to the output.
// Comment this out if you don't use it.
$bm = Profiler::app_total();
$response->body(
	str_replace(
		array('{exec_time}', '{mem_usage}'),
		array(round($bm[0], 4), round($bm[1] / pow(1024, 2), 3)),
		$response->body()
	)
);

$response->send(true);
