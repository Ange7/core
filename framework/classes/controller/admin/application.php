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

class Controller_Admin_Application extends Controller_Admin_Auth {

    public $template    = 'nos::admin/html';
    public $bypass      = false;


    public function before($response = null) {
        $ret = parent::before($response);
        $location = $this->getLocation();
        list($application, $file_name) = $location;
        if ($application == 'nos' && isset($location[2])) {
            $application = 'nos_'.$location[2]; // this hack should be temporary until we figure out how to correctly implement native applications...
        }

        if (!$this->bypass && $application != 'nos' && !Permission::check($application, 'access')) {
            throw new \Exception('You don\'t have access to module '.$application.'!');
        }

        //\Debug::dump($application, Permission::check($application, 'access'));
        return $ret;
    }


    public function after($response) {
        foreach (array(
                     'title' => 'Administration',
                     'base' => \Uri::base(false),
                     'require'  => 'static/novius-os/admin/vendor/requirejs/require.js',
                 ) as $var => $default) {
            if (empty($this->template->$var)) {
                $this->template->$var = $default;
            }
        }
        $ret = parent::after($response);
        $this->template->set(array(
            'css' => \Asset::render('css'),
            'js'  => \Asset::render('js'),
        ), false, false);
        return $ret;
    }
}