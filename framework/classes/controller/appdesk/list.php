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

use Fuel\Core\Request;

use Asset, Format, Input, Session, View, Uri;

/**
 * The cloud Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Appdesk_List extends Controller_Generic_Admin {

	protected $appdesk = array();

    public function before($response = null) {
        parent::before($response);
        if (!isset($this->config['appdesk'])) {
            list($module_name, $file_name) = $this->getLocation();
            $file_name = explode('/', $file_name);
            array_splice($file_name, count($file_name) - 1, 0, array('appdesk'));
            $file_name = implode('/', $file_name);
        } else {
            list($module_name, $file_name) = explode('::', $this->config['appdesk']);
        }

		$this->appdesk = \Config::mergeWithUser($module_name.'::'.$file_name, static::loadConfiguration($module_name, $file_name));
    }

	public function action_index($view = null) {
		if (!\Cms\Auth::check()) {
			\Response::redirect('/admin/cms/login?redirect='.urlencode($_SERVER['REDIRECT_URL']));
			exit();
		}

        if (empty($view)) {
            $view = \Input::get('view', $this->appdesk['selectedView']);
        }
        $this->appdesk['selectedView'] = $view;

        if (empty($this->appdesk['custom'])) {
            $this->appdesk['custom'] = array(
                'from' => 'default',
            );
        }

		$view = View::forge('appdesk/list');

		$locales = \Config::get('locales', array());

        $view->set('appdesk', \Format::forge(array_merge(array('locales' => $locales), $this->appdesk))->to_json(), false);
		return $view;
	}

    public function action_json()
    {

		if (!\Cms\Auth::check()) {
			\Response::json(403, array(
				'login_page' => \Uri::base(false).'admin/cms/login',
			));
		}

	    $config = $this->appdesk;
	    $where = function($query) use ($config) {
		    foreach ($config['inputs'] as $input => $condition) {
			    $value = Input::get('inspectors.'.$input);
			    if (is_callable($condition)) {
				    $query = $condition($value, $query);
			    }
		    }

		    $value = Input::get('inspectors.search');
		    $condition = $config['search_text'];
		    if (is_callable($condition)) {
			    $query = $condition($value, $query);
		    } else if (is_array($condition)) {
			    $query->and_where_open();
				foreach ($condition as $field) {
					$query->or_where(array($field, 'LIKE', '%'.$value.'%'));
				}
			    $query->and_where_close();
		    } else {
			    $query->where(array($condition, 'LIKE', '%'.$value.'%'));
		    }

		    Filter::apply($query, $config);

		    return $query;
	    };

	    $return = $this->items(array_merge($this->appdesk['query'], array(
		    'callback' => array($where),
		    'dataset' => $this->appdesk['dataset'],
		    'lang' => Input::get('lang', null),
	        'limit' => intval(Input::get('limit', \Arr::get($this->appdesk['query'], 'limit'))),
	        'offset' => intval(Input::get('offset', 0)),
	    )));

        $json = array(
            'get' => '',
            'query' =>  '',
	        'query2' =>  '',
            'offset' => $return['offset'],
            'items' => $return['items'],
            'total' => $return['total'],
        );

        if (\Fuel::$env === \Fuel::DEVELOPMENT) {
            $json['get'] = Input::get();
            $json['query'] = $return['query'];
	        $json['query2'] = $return['query2'];
        }
        if (\Input::get('debug') !== null) {
            \Debug::dump($json);
            exit();
        }

        \Response::json($json);
    }

	protected function searchtext_condition($menu, $target, $search)
	{
		if ($target) {
			if ($menu['target'] == $target) {
				if (isset($menu['column'])) {
					return array(array($menu['column'], 'like', '%'.$search.'%'));
				} else if (isset($menu['submenu']) && is_array($menu['submenu'])) {
					$wheres = array();
					foreach ($menu['submenu'] as $smenu) {
						$wheres = array_merge($wheres, $this->searchtext_condition($smenu, false, $search));
					}
					return $wheres;
				}
			} else if (isset($menu['submenu']) && is_array($menu['submenu'])) {
				foreach ($menu['submenu'] as $smenu) {
					$where = $this->searchtext_condition($smenu, $target, $search);
					if (count($where)) {
						return $where;
					}
				}
			}
		} else {
			if (isset($menu['column'])) {
				return array(array($menu['column'], 'like', '%'.$search.'%'));
			} else if (isset($menu['submenu']) && is_array($menu['submenu'])) {
				$wheres = array();
				foreach ($menu['submenu'] as $smenu) {
					$wheres = array_merge($wheres, $this->searchtext_condition($smenu, false, $search));
				}
				return $wheres;
			}
		}
		return array();
	}

	public function action_tree_json()
	{
		if (!\Cms\Auth::check()) {
			\Response::json(403, array(
				'login_page' => \Uri::base(false).'admin/cms/login',
			));
		}

        $tree_config = $this->appdesk['tree'];
        $tree_config['id'] =  $this->appdesk['configuration_id'];

		$json = $this->tree(array_merge(array('id' => $this->appdesk['configuration_id']), $this->appdesk['tree']));

		if (\Fuel::$env === \Fuel::DEVELOPMENT) {
			$json['get'] = Input::get();
		}
		if (\Input::get('debug') !== null) {
			\Debug::dump($json);
			exit();
		}

		\Response::json($json);
	}
}

/* End of file list.php */
