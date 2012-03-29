<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Cms;

class Permission_Select extends Permission_Driver {

	protected $choices;

	protected $permissions;

	protected static $data;

	protected function set_options($options = array()) {

		$this->choices = $options['choices'];
	}

	public function check($role, $key) {
		static::_load_permissions();
		return isset(static::$data[$role->role_id][$this->module]) && in_array($key, (array) static::$data[$role->role_id][$this->module]);
	}

	public function display($role) {
		echo \View::forge('cms::permission/driver/select', array(
			'role'      => $role,
			'module'     => $this->module,
			'identifier' => $this->identifier,
			'choices'    => $this->choices,
			'driver'     => $this,
		), false);
	}

	public function save($role, $data) {

		$perms = Model_User_Permission::find('all', array(
            'where' => array(
                array('perm_role_id', $role->role_id),
				array('perm_module', $this->module),
                array('perm_identifier', $this->identifier),
            ),
        ));

		// Remove old permissions
        foreach ($perms as $p) {
            $p->delete();
        }

		// Add appropriates one
        foreach ($data as $permitted) {
			$p = new Model_User_Permission();
			$p->perm_role_id   = $role->role_id;
			$p->perm_module     = $this->module;
			$p->perm_identifier = $this->identifier;
			$p->perm_key = $permitted;
			$p->save();
        }
	}

	protected static function _load_permissions() {
		if (!empty(static::$data)) {
			return;
		}

        $role_ids = array();
        foreach (Model_User_Role::find('all') as $g) {
            $role_ids[] = $g->role_id;
        }
        $data = Model_User_Permission::find('all', array(
            'where' => array(
                array('perm_role_id', 'IN', $role_ids),
            ),
        ));

        foreach ($data as $d) {
            static::$data[$d->perm_role_id][$d->perm_module][] = $d->perm_key;
        }
	}

}
