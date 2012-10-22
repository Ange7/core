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

class Controller_Admin_Auth extends Controller
{

    public function before()
    {
        parent::before();

        if (!\Nos\Auth::check()) {
            if (\Input::is_ajax()) {
                $this->response(array(
                        'login_page' => \Uri::base(false).'admin/nos/login',
                    ), 403);
            } else {
                \Response::redirect('admin/nos/login?'.http_build_query(array(
                            'redirect' => mb_substr(\Input::server('REDIRECT_SCRIPT_URL', \Input::server('REDIRECT_URL', 'admin/')), defined('NOS_RELATIVE_DIR') ?  mb_strlen(NOS_RELATIVE_DIR) : 0).'?tab='.\Input::get('tab', ''),
                        ), '', '&'));
                exit();
            }
        }
    }
}
