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

use Fuel\Core\Arr;
use Fuel\Core\Config;

class Controller_Admin_Page_Inspector_Lang extends Controller_Inspector_Lang {

	public function before($response = null) {
        Config::load('nos::admin/page', true);
		$this->config = Arr::merge($this->config, Config::get('nos::admin.page.page.filters.lang'));

		parent::before($response);
	}
}