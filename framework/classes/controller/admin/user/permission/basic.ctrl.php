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

use View;

class Controller_Admin_User_Permission_Basic extends Controller
{
    public function action_edit($role_id, $app)
    {
        $role = Model_User_Role::find($role_id);

        \Config::load("$app::permissions", true);
        $permissions = \Config::get("$app::permissions", array());

        return View::forge('admin/user/permission/basic', array(
            'role' => $role,
            'app'   => $app,
            'permissions' => $permissions,
        ));
    }

    protected function post_edit()
    {
        $perms = Model_User_Permission::find('all', array(
            'where' => array(
                array('perm_role_id', $_POST['role_id']),
            ),
        ));
        foreach ($perms as $p) {
            $p->delete();
        }

        if (empty($_POST['app'])) {
            return;
        }
        foreach ($_POST['app'] as $app => $keys) {
            if (!in_array('access', $keys)) {
                continue;
            }
            foreach ($keys as $key) {
                $p = new Model_User_Permission();
                $p->perm_role_id = $_POST['role_id'];
                $p->perm_application = $app;
                $p->perm_key = $key;
                $p->save();
            }
        }
    }
}
