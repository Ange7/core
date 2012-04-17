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

use Fuel\Core\File;
use Fuel\Core\View;

class Controller_Admin_Tray_Account extends \Controller {

    public function action_index() {
		$user = \Session::get('logged_user');
		$fieldset_infos    = Controller_Admin_User_Form::fieldset_edit($user)->set_config('field_template', '<tr><th>{label}{required}</th><td class="{error_class}">{field} {error_msg}</td></tr>'); // static::fieldset_edit($user)->set_config('field_template', '<tr><th>{label}{required}</th><td class="{error_class}">{field} {error_msg}</td></tr>');
        $fieldset_display  = static::fieldset_display($user)->set_config('field_template', '<tr><th>{label}{required}</th><td class="{error_class}">{field} {error_msg}</td></tr>');

        return View::forge('tray/account', array(
			'logged_user' => $user,
			'fieldset_infos' => $fieldset_infos,
            'fieldset_display' => $fieldset_display,
		), false);
	}

	public function action_disconnect() {
		\Session::destroy();
		\Response::redirect('/admin/nos/login/reset');
		exit();
	}

    public static function fieldset_display($user) {

		$configuration = $user->getConfiguration();
        $fields = array (
            'background' => array (
                'label' => 'Wallpaper',
                'widget' => 'Nos\Widget_Media',
				'form' => array(
					'value' => \Arr::get($configuration, 'misc.display.background', ''),
				),
            ),
            'save' => array(
                'label' => '',
                'form' => array(
                    'class' => 'primary',
                    'data-icon' => 'check',
                    'type' => 'submit',
                    'tag' => 'button',
                    'value' => __('Save'),
                ),
            ),
        );

        $fieldset_display = \Fieldset::build_from_config($fields, $user, array(
            'form_name' => 'edit_user_display',
            'complete' => function($data) use ($user) {

				$body = array();

                try {
                    $configuration = $user->getConfiguration();
					if (!empty($data['background'])) {
						$media = Model_Media_Media::find($data['background']);
						if (!empty($media)) {
							\Arr::set($configuration, 'misc.display.background', $data['background']);
							$notify = strtr(__('Your wallpaper is now "{title}"'), array(
                                '{title}' => $media->media_title,
                            ));
							$body['wallpaper_url'] = \Uri::create($media->get_public_path());
						} else {
							$data['background'] = null;
							$error = __('The selected image does not exists.');
						}
                    }
					if (empty($data['background'])) {
						\Arr::delete($configuration, 'misc.display.background');
						$notify = __('Your wallpaper has been removed.');
					}

                    $user->user_configuration = serialize($configuration);
                    $user->save();
                } catch (\Exception $e) {
                    $error = \Fuel::$env == \Fuel::DEVELOPMENT ? $e->getMessage() : 'An error occured.';
                }

				if (!empty($notify)) {
					$body['notify'] = $notify;
				}
				if (!empty($error)) {
					$body['error'] = $error;
				}

                \Response::json($body);
            }
        ));
        $fieldset_display->js_validation();
        return $fieldset_display;
    }
}