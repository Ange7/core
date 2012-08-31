<?php
return array(
    'behaviours' => array (
        'Nos\Orm_Behaviour_Sharable' => array(
            'data' => array(
                \Nos\DataCatcher::TYPE_TITLE => array(
                    'value' => 'page_title',
                    'useTitle' => __('Title'),
                ),
                \Nos\DataCatcher::TYPE_URL => array(
                    'value' => function($page) {
                        return $page->get_href(array('absolute' => true));
                    },
                    'options' => function($page) {
                        return array($page->get_href(array('absolute' => true)));
                    },
                    'useTitle' => __('Url'),
                ),
                \Nos\DataCatcher::TYPE_IMAGE => array(
                    'value' => function($page) {
                        $possible = $page->possible_medias();

                        return Arr::get(array_keys($possible), 0, null);
                    },
                    'possibles' => function($page) {
                        return $page->possible_medias();
                    },
                ),
            ),
        ),
    ),
);
