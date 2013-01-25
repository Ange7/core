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

class Orm_Behaviour_Virtualpath extends Orm_Behaviour_Virtualname
{
    public static function _init()
    {
        I18n::current_dictionary('nos::orm');
    }

    protected $_parent_relation = null;

    protected $_properties = array();
    protected $_data_diff = array();

    public function __construct($class)
    {
        parent::__construct($class);
        $this->_properties['unique_path'] = $this->_properties['unique'];
        $this->_properties['unique'] = false;
        if (is_array($this->_properties['extension_property'])) {
            $this->_properties['extension_property'] = array_merge(array(
                'before' => '.',
                'after' => '',
            ), $this->_properties['extension_property']);

            if (empty($this->_properties['extension_property']['property'])) {
                throw new \Exception('Extension property not found by virtualname behaviour: '.$this->_class);
            }
        }

        $tree = $class::behaviours('Nos\Orm_Behaviour_Tree', false);
        if (!empty($tree)) {
            $this->_properties['level_property'] = \Arr::get($tree, 'level_property', false);
        }

        if (!empty($this->_properties['parent_relation'])) {
            $this->_parent_relation = $class::relations($this->_properties['parent_relation']);
        }

        if (false === $this->_parent_relation) {
            throw new \Exception('Relation "parent" not found by virtualname behaviour: '.$this->_class);
        }
    }

    public function change_parent(\Nos\Orm\Model $item)
    {
        $parent = $item->get_parent();
        $parent_path = $parent !== null ? $parent->{$this->_properties['virtual_path_property']} : '';
        if (!empty($this->_properties['extension_property']) && !empty($parent_path)) {
            $parent_path = preg_replace('`'.$this->extension($item).'$`iUu', '', $parent_path).'/';
        }
        $item->{$this->_properties['virtual_path_property']} = $parent_path;
        $item->{$this->_properties['virtual_path_property']} .= $item->{$this->_properties['virtual_name_property']};
        if (!empty($this->_properties['extension_property'])) {
            $item->{$this->_properties['virtual_path_property']} .= $this->extension($item);
        }
    }

    public function before_save(\Nos\Orm\Model $item)
    {
        parent::before_save($item);
        $diff = $item->get_diff();

        // If the parent changes, then the virtual path changes
        if (!empty($this->_parent_relation)) {
            $key_from = $this->_parent_relation->key_from[0];
            // Compare $diff[0] and $diff[1] because the former can be (string) and latter (int), even if it's the same value
            $parent_has_changed = array_key_exists($key_from, $diff[0]) && $diff[0][$key_from] != $diff[1][$key_from];
            if ($item->is_new() || $parent_has_changed) {
                $class = $this->_parent_relation->model_to;
                $parent = null;
                if (!empty($item->{$this->_parent_relation->key_from[0]})) {
                    $parent = $class::find($item->{$this->_parent_relation->key_from[0]});
                }
                $item->{$this->_properties['virtual_path_property']} = ($parent !== null ? $parent->virtual_path(true) : '') . $this->virtual_name($item) . $this->extension($item);
                $this->_data_diff[$item->{$this->_properties['virtual_path_property']}] = $diff;
            }
        }

        if (!empty($diff[1][$this->_properties['virtual_name_property']])) {
            $virtual_path = $this->_properties['virtual_path_property'];
            $diff[0][$virtual_path] = $item->{$virtual_path};
            $old_name = $diff[0][$virtual_path];
            $new_name = $item->{$virtual_path}.$this->extension($item);
            if (!empty($old_name)) {
                $old_name .= $this->extension($item, true);
                $item->{$this->_properties['virtual_path_property']} = preg_replace('`'.preg_quote($old_name).'$`iUu', $new_name, $item->{$virtual_path});
            }
            $diff[1][$virtual_path] = $item->{$this->_properties['virtual_path_property']};
            $this->_data_diff[$item->{$virtual_path}] = $diff;
        }

        // Check uniqueness
        if ($this->_properties['unique_path']) {
            if (!empty($diff[0][$this->_properties['virtual_path_property']])) {
                $where = array(
                    array($this->_properties['virtual_path_property'], $item->{$this->_properties['virtual_path_property']})
                );
                if (is_array($this->_properties['unique_path']) && !empty($this->_properties['unique_path']['context_property'])) {
                    $where[] = array($this->_properties['unique_path']['context_property'], '=', $item->{$this->_properties['unique_path']['context_property']});
                }
                if (!$item->is_new()) {
                    $pk = \Arr::get($item::primary_key(), 0);
                    $where[] = array($pk, '!=', $item->{$pk});
                }

                $duplicate = $item::find('all', (array('where' => $where)));
                if (!empty($duplicate)) {
                    throw new BehaviourDuplicateException(__('This URL is already used. Since an URL must be unique, you’ll have to choose another one. Sorry about that.'));
                }
            }
        }
    }

    public function after_save(\Nos\Orm\Model $item)
    {
        if (isset($this->_data_diff[$item->{$this->_properties['virtual_path_property']}])) {
            $diff = $this->_data_diff[$item->{$this->_properties['virtual_path_property']}];

            $old_virtual_path = \Arr::get($diff[0], $this->_properties['virtual_path_property'], $item->{$this->_properties['virtual_path_property']});
            if (!empty($this->_properties['extension_property'])) {
                $old_virtual_path = preg_replace('`'.$this->extension($item).'$`iUu', '/', $old_virtual_path);
            }
            $new_virtual_path = $this->virtual_path($item, true);

            // Change the virtual path for all items
            $replaces = array(
                $this->_properties['virtual_path_property'] => \DB::expr('REPLACE('.$this->_properties['virtual_path_property'].', '.\DB::escape($old_virtual_path).', '.\DB::escape($new_virtual_path).')'),
            );

            // Level property also could have to be updated
            if (!empty($this->_properties['level_property'])) {
                $diff_level = \Arr::get($diff[0], $this->_properties['level_property'], $item->{$this->_properties['level_property']}) - $item->{$this->_properties['level_property']};
                if ($diff_level != 0) {
                    $replaces[$this->_properties['level_property']] = \DB::expr($this->_properties['level_property'].' '.($diff_level > 0 ? '-' : '+').' '.abs($diff_level));
                }
            }

            \DB::update($item->table())
                ->set($replaces)
                ->where($this->_properties['virtual_path_property'], 'LIKE', $old_virtual_path.'%')
                ->execute();
        }
    }

    public function virtual_path(\Nos\Orm\Model $item, $dir = false)
    {
        if (!empty($this->_properties['virtual_path_property'])) {
            $path = $item->{$this->_properties['virtual_path_property']};
        } else {
            $path = '';
        }
        if ($dir) {
            $path = preg_replace('`'.preg_quote($this->extension($item)).'$`iUu', '', $path).'/';
        }

        return $path;
    }

    public function extension(\Nos\Orm\Model $item, $old = false)
    {
        if (!empty($this->_properties['extension_property'])) {
            if (is_array($this->_properties['extension_property'])) {
                $ext = $item->{$this->_properties['extension_property']['property']};
                if ($old) {
                    $diff = $item->get_diff();
                    if (!empty($diff[0][$this->_properties['extension_property']['property']])) {
                        $ext = $diff[0][$this->_properties['extension_property']['property']];
                    }
                }

                return $this->_properties['extension_property']['before'].$ext.$this->_properties['extension_property']['after'];
            } else {
                return $this->_properties['extension_property'];
            }
        }
        return '';
    }
}
