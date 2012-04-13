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

class Controller_Admin_Page_Page extends Controller {

    public function action_add() {

        $parent = Model_Page_Page::find(\Input::post('page_parent_id', 1));
        $page   = Model_Page_Page::forge();

        $fields = \Config::load('nos::controller/admin/page/form_page', true);
        $fields = \Arr::merge($fields, array(
            'page_title' => array(
                'validation' => array(
                    'required',
                    'min_length' => array(6),
                ),
            ),
            'page_parent_id' => array(
                'form' => array(
                    'type' => 'hidden',
                    'value' => $parent->page_id,
                ),
            ),
            'page_lang' => array(
                'form' => array(
                    'type' => 'hidden',
                ),
            ),
            'page_lang_common_id' => array(
                'form' => array(
                    'type' => 'hidden',
                ),
            ),
			'save' => array(
				'form' => array(
					'value' => __('Add'),
				),
			),
        ));

		$fieldset = \Fieldset::build_from_config($fields, $page, array(
            'before_save' => function($page, $data) {
                $parent = $page->find_parent();
                // Event 'after_change_parent' will set the appropriate lang
                //\Debug::dump($parent->id, $parent->get_lang(), $page->get_lang());
                //\Debug::dump($parent->find_lang('en_GB')->id);
                $page->set_parent($parent);
                $page->page_level = $parent->page_level + 1;

                foreach (\Input::post('wysiwyg', array()) as $key => $text) {
                    $page->wysiwygs->$key = $text;
                }
            },
            'success' => function() use ($page) {
                return array(
                    'notify' => 'Page sucessfully added.',
                    'dispatchEvent' => array(
                        'event' => 'reload',
                        'target' => 'nos_page',
                    ),
                    'replaceTab' => 'admin/nos/page/page/edit/'.$page->page_id,
                );
            }
        ));
		$fieldset->js_validation();

        return \View::forge('nos::admin/page/page_add', array(
			'parent'   => $parent,
			'page'     => $page,
            'parent'   => $parent,
			'fieldset' => $fieldset,
		), false);
    }

    public function action_form() {

        $create_from_id = \Input::get('create_from_id', 0);
        if (empty($create_from_id)) {
            $page                 = Model_Page_Page::forge();
            $page->page_lang_common_id = \Input::get('common_id');
        } else {
             $page_from = Model_Page_Page::find($create_from_id);
             $page      = clone $page_from;
        }
        $page->page_lang = \Input::get('lang');
        $parent_page = Model_Page_Page::find($page->page_lang_common_id)->find_parent();
        if (!empty($page->page_lang)) {
            $parent_page = $parent_page->find_lang($page->page_lang);
        }
        $page->page_parent_id = $parent_page->page_id;

        $fields = \Config::load('nos::controller/admin/page/form_page', true);
        $fields = \Arr::merge($fields, array(
            'page_title' => array(
                'validation' => array(
                    'required',
                    'min_length' => array(6),
                ),
            ),
            'page_lang' => array(
                'form' => array(
                    'type' => 'hidden',
                    'value' => \Input::get('lang'),
                ),
            ),
            'page_lang_common_id' => array(
                'form' => array(
                    'type' => 'hidden',
                    'value' => $page->page_lang_common_id,
                ),
            ),
            'page_parent_id' => array(
                'widget_options' => array(
                    'lang' => $page->page_lang,
                ),
            ),
			'save' => array(
				'form' => array(
					'value' => __('Add'),
				),
			),
        ));

        if (!empty($create_from_id)) {
            $fields['create_from_id'] = array(
                'form' => array(
                    'type' => 'hidden',
                    'value' => $create_from_id,
                ),
            );
        }

		$fieldset = \Fieldset::build_from_config($fields, $page);
		$fieldset->js_validation();

        return \View::forge('nos::admin/page/page_form', array(
			'item'     => $page,
			'fieldset' => $fieldset,
            'lang'     => $page->page_lang
		), false);
    }

    public function action_edit($id) {

        $page = Model_Page_Page::find($id);

        $fields = \Config::load('nos::controller/admin/page/form_page', true);
        \Arr::set($fields, 'id.form.value', $page->page_id);

		$fieldset = \Fieldset::build_from_config($fields, $page, array(
            'before_save' => function($page, $data) {
                $parent = $page->find_parent();
                // Event 'after_change_parent' will set the appropriate lang
                //\Debug::dump($parent->id, $parent->get_lang(), $page->get_lang());
                //\Debug::dump($parent->find_lang('en_GB')->id);
                $page->set_parent($parent);
                $page->page_level = $parent === null ? 1 : $parent->page_level + 1;

                foreach (\Input::post('wysiwyg', array()) as $key => $text) {
                    $page->wysiwygs->$key = $text;
                }
            },
            'success' => function() {
                return array(
                    'notify' => 'Page sucessfully saved.',
                    'dispatchEvent' => array(
                        'event' => 'reload',
                        'target' => 'nos_page',
                    ),
                );
            }
        ));
		$fieldset->js_validation();
        $fieldset->set_config('field_template', '<tr><th>{label}{required}</th><td class="{error_class}">{field} {error_msg}</td></tr>');

        return \View::forge('nos::admin/page/page_edit', array(
			'page'     => $page,
			'fieldset' => $fieldset,
		), false);
    }

    protected static function  _get_page_with_permission($page_id, $permission) {
        if (empty($page_id)) {
            throw new \Exception('No page specified.');
        }
        $page = Model_Page_Page::find($page_id);
        if (empty($page)) {
            throw new \Exception('Page not found.');
        }
        if (!static::check_permission_action('delete', 'controller/admin/page/appdesk/list', $page)) {
            throw new \Exception('Permission denied');
        }
        return $page;
    }

	public function action_delete_page($page_id = null) {
        try {
            $page = static::_get_page_with_permission($page_id, 'delete');
            return \View::forge('nos::admin/page/page_delete', array(
                'page' => $page,
            ));
        } catch (\Exception $e) {
            // Easy debug
            if (\Fuel::$env == \Fuel::DEVELOPMENT && !\Input::is_ajax()) {
                throw $e;
            }
			$body = array(
				'error' => $e->getMessage(),
			);
            \Response::json($body);
		}
    }

	public function action_delete_page_confirm() {
        try {
            $page_id = \Input::post('id');
            // Allow GET for easier dev
            if (empty($page_id) && \Fuel::$env == \Fuel::DEVELOPMENT) {
                $page_id = \Input::get('id');
            }

            $page = static::_get_page_with_permission($page_id, 'delete');
            // Delete all languages by default
            $lang = \Input::post('lang', 'all');

            // Delete children for all languages
            if ($lang == 'all') {
                // Children will be deleted recursively (with the 'after_delete' event from the Tree behaviour)
                // Optimised operation for deleting all languages
                $page->delete_all_lang();
            } else {
                // Search for the appropriate page
                if ($lang != 'all' && $page->get_lang() != $lang) {
                    $page = $page->find_lang($lang);
                }

                if (empty($page)) {
                    throw new \Exception(strtr(__('The page has not been found in the requested language {language}'), array(
                        '{language}' => $lang,
                    )));
                }

                // Reassigns common_id if this item is the main language (with the 'after_delete' event from the Translatable behaviour)
                // Children will be deleted recursively (with the 'after_delete' event from the Tree behaviour)
                $page->delete();
            }

			$body = array(
				'notify' => 'Page successfully deleted.',
                'dispatchEvent' => array(
	                'event' => 'reload',
                    'target' => 'nos_page',
                ),
			);

        } catch (\Exception $e) {
            // Easy debug
            if (\Fuel::$env == \Fuel::DEVELOPMENT && !\Input::is_ajax()) {
                throw $e;
            }
			$body = array(
				'error' => $e->getMessage(),
			);
		}

        \Response::json($body);
    }
}