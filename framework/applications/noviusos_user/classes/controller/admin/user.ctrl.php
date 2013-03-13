<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Nos\User;

class Controller_Admin_User extends \Nos\Controller_Admin_Crud
{
    public function prepare_i18n()
    {
        parent::prepare_i18n();
        \Nos\I18n::current_dictionary('noviusos_user::common');
    }

    public function before()
    {
        if (\Request::active()->action == 'insert_update' && ($user = \Session::user()) && isset(\Request::active()->route->method_params[0]) && \Request::active()->route->method_params[0] == $user->user_id) {
            $this->bypass = true;
        }
        parent::before();
    }

    protected function fields($fields)
    {
        $fields = parent::fields($fields);
        if ($this->is_new) {
            $fields['user_password']['validation'][] = 'required';
            $fields['password_confirmation']['validation']['match_field'] = array('user_password');
            $fields['password_confirmation']['validation'][] = 'required';
            $fields['user_last_connection']['dont_populate'] = true;
            $fields['user_last_connection']['dont_save'] = true;
        } else {
            unset($fields['user_password']);
            $this->config['i18n']['notification item saved'] = __('Done, your password has been changed.');
        }

        return $fields;
    }

    public function save($item, $data)
    {
        if (!$this->is_new && $item->is_changed('user_password')) {
            $this->config['messages']['successfully saved'] = __('Done, your password has been changed.');
        }

        return parent::save($item, $data);
    }

    public function action_save_permissions()
    {
        $role = Model_Role::find(\Input::post('role_id'));

        $permissions = \Input::post('perm');
        foreach ($permissions as $perm_name => $allowed) {
            $allowed = array_keys($allowed);

            $olds = Model_Permission::find('all', array('where' => array(
                array('perm_role_id', $role->role_id),
                array('perm_name',    $perm_name),
            )));
            $existing = array();

            // Delete old authorisations
            foreach ($olds as $old) {
                if (!in_array($old->perm_category_key, $allowed)) {
                    $old->delete();
                } else {
                    $existing[] = $old->perm_category_key;
                }
            }

            // Add new authorisations
            foreach ($allowed as $category_key) {
                if (!in_array($category_key, $existing)) {
                    $new = new Model_Permission();
                    $new->perm_role_id      = $role->role_id;
                    $new->perm_name         = $perm_name;
                    $new->perm_category_key = $category_key;
                    $new->save();
                }
            }
        }
        \Response::json(array(
            'notify' => __('OK, permissions saved.'),
        ));

    }
}
