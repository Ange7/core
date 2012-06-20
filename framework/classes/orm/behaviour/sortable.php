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

class Orm_Behaviour_Sortable extends Orm_Behaviour
{
	/**
	 * sort_property
	 */
	protected $_properties = array();

	public function before_query(&$options) {
		if (array_key_exists('order_by', $options)) {
			$order_by = $options['order_by'];
	        if (!empty($order_by['default_sort'])) {
	            unset($order_by['default_sort']);
	            $order_by[$this->_properties['sort_property']] = \Arr::get($this->_properties, 'sort_order', 'ASC');
	        }
			$options['order_by'] = $order_by;
		}
	}

    /**
     * Sets a new parent for the object
     *
     * @param   Orm\Model The parent object
     * @return  void
     */
	public function move_before($object, $before = null) {
        $this->_move($object, $before->get_sort() - 0.5);
	}

	public function move_after($object, $before = null) {
        $this->_move($object, $before->get_sort() + 0.5);
	}

	public function move_to_last_position($object) {
        $this->_move($object, 10000);
	}

    public function get_sort(\Nos\Orm\Model $obj) {
        $sort_property = $this->_properties['sort_property'];
        return $obj->get($sort_property);
    }

    public function set_sort(\Nos\Orm\Model $obj, $sort) {
        $sort_property = $this->_properties['sort_property'];
        $obj->set($sort_property, $sort);
    }

    protected function _move($object, $sort) {
        $sort_property = $this->_properties['sort_property'];
        $object->set($sort_property, $sort);
        $object->observe('before_sort');
        $object->save();
        $object->observe('after_sort');
    }

    public function after_sort(\Nos\Orm\Model $obj) {
        $tree = $obj->behaviours('Nos\Orm_Behaviour_Tree');
        $sort_property = $this->_properties['sort_property'];
        $conditions = array();
        if (!empty($tree)) {
            $conditions[] = array('parent', $obj->get_parent());
        }
        $i = 1;
        $unsorted = $obj::find('all', array(
            'where' => $conditions,
            'order_by' => array('default_sort' => 'ASC')
        ));
        foreach ($unsorted as $u) {
            $u->set($sort_property, $i++);
            $u->save();
            $updated[$u->get_sort()] = $u->id;
        }
    }
}