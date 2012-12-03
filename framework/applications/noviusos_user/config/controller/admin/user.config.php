<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */
return array(
    'controller_url' => 'admin/noviusos_user/user',
    'model' => 'Nos\\User\\Model_User',
    'i18n_file' => 'nos::user',
    'layout_insert' => array(
        array(
            'view' => 'noviusos_user::admin/user_add',
        ),
    ),
    'views' => array(
        'update' => 'noviusos_user::admin/user_edit',
        'delete' => 'noviusos_user::admin/delete_popup',
    ),
    'fields' => array(
        'user_id' => array(
            'label' => __('ID: '),
            'renderer' => 'Nos\Renderer_Text',
        ),
        'user_name' => array(
            'label' => __('Family name'),
            'renderer' => '',
            'validation' => array(
                'required',
            ),
        ),
        'user_firstname' => array(
            'label' => __('First name'),
            'renderer' => '',
            'validation' => array(
                'required',
            ),
        ),
        'user_email' => array(
            'label' => __('Email: '),
            'renderer' => '',
            'validation' => array(
                'required',
                'valid_email',
            ),
        ),
        'user_last_connection' => array(
            'label' => __('Last login: '),
            'add' => false,
            'renderer' => 'Nos\Renderer_Date_Select',
            'form' => array(
                'readonly' => true,
                'date_format' => 'eu_full',
            ),
        ),
        'user_expert' => array(
            'label' => __('Expert view '),
            'form' => array(
                'type' => 'checkbox',
                'value' => '1',
                'empty' => '0',
            ),
        ),
        'user_password' => array(
            'label' => __('Password: '),
            'form' => array(
                'type' => 'password',
                'size' => 20,
                'value' => '',
            ),
            'validation' => array(
                'min_length' => array(6),
            ),
        ),
        'password_reset' => array(
            'label' => __('Password: '),
            'form' => array(
                'type' => 'password',
                'size' => 20,
                'value' => '',
            ),
            'before_save' =>
                function ($item, $data)
                {
                    if (!empty($data['password_reset'])) {
                        $item->user_password = $data['password_reset'];
                    }
                },
            'validation' => array(
                'min_length' => array(6),
            ),
        ),
        'password_confirmation' => array(
            'label' => 'Password (confirmation)',
            'form' => array(
                'type' => 'password',
                'size' => 20,
            ),
            'before_save' =>
                function ($item, $data)
                {
                },
            'validation' => array(
                'match_field' => array('user_password'),
            ),
        ),
        'save' => array(
            'label' => '',
            'form' => array(
                'type' => 'submit',
                'tag' => 'button',
                'data-icon' => 'check',
                'class' => 'primary',
                'value' => __('Save'),
            ),
        ),
    ),
);
