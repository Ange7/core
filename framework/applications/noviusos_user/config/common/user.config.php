<?php

Nos\I18n::current_dictionary(array('noviusos_user::common', 'nos::common'));

return array(
    'data_mapping' => array(
        'fullname' => array(
            'title' => __('Name'),
            'value' => function($item) {
                return $item->fullname();
            },
            'search_column' => 'user_firstname',
            'cellFormatters' => array(
                'link' => array(
                    'type' => 'link',
                    'action' => 'Nos\User\Model_User.edit',
                ),
            ),
        ),
        'user_email' => array(
            'title' => __('Email'),
        ),
        'id_permission' => array(
            'visible' => false,
            'value' => function($item) {
                return $item->roles && reset($item->roles)->role_id ?: $item->user_id;
            },
        ),
    ),
    'i18n' => array(
        // Crud
        'notification item added' => __('Done, the user has been added.'),
        'notification item deleted' => __('The user has been deleted.'),

        // General errors
        'notification item does not exist anymore' => __('This user doesn’t exist any more. It has been deleted.'),
        'notification item not found' => __('We cannot find this user.'),

        // Deletion popup
        'deleting item title' => __('Deleting the user ‘{{title}}’'),
    ),
    'actions' => array(
        'Nos\User\Model_User.add' => array(
            'label' => __('Add a user'),
        ),
        'Nos\User\Model_User.edit' => array(
            'action' => array(
                'tab' => array(
                    'label' => '{{fullname}}',
                ),
            ),
        ),
    ),
);