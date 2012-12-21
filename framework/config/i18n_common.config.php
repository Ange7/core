<?php

Nos\I18n::current_dictionary('nos::common');

return array(
    // Crud
    'notification item added' => __('Done! The item has been added.'),
    'notification item saved' => __('OK, all changes are saved.'),
    'notification item deleted' => __('The item has been deleted.'),

    // General errors
    'notification item does not exist anymore' => __('This item doesn’t exist any more. It has been deleted.'),
    'notification item not found' => __('We cannot find this item.'),
    'deleted popup title' => __('Bye bye'),
    'deleted popup close' => __('Close tab'),

    // Blank slate
    'translate error parent not available in context' => __('We’re afraid this item cannot be added in {{context}} because its <a>parent</a> is not available in this context yet.'),
    'translate error parent not available in language' => __('We’re afraid this item cannot be added in {{language}} because its <a>parent</a> is not available in this language yet.'),
    'translate error impossible context' => __('This item cannot be added in {{context}}. (How come you get this error message? You’ve hacked your way into here, haven’t you?)'),

    // Deletion popup
    'deleting item title' => __('Deleting the item ‘{{title}}’'),
    'deleting confirmation' => __('Last chance, there’s no undo. Do you really want to delete this item?'),

    # Delete action's labels
    'deleting confirmation button' => __('{{Button}} or <a>cancel</a>'),
    'deleting confirmation item' => __('Confirm deletion'),
    'deleting button 0 items' => __('Nothing to delete'),
    'deleting button 1 item' => __('Delete this item'),
    'deleting button N items' => __('Delete these {{count}} items'),

    'deleting wrong confirmation' => __('We cannot delete this item as the number of sub-items you’ve entered is wrong. Please amend it.'),

    '1 item' => __('1 item'),
    'N items' => __('{{count}} items'),

    # Keep only if the model has the behaviour Contextable
    'deleting with N contexts' => __('This item exists in <strong>{{context_count}} contexts</strong>.'),
    'deleting with N languages' => __('This item exists in <strong>{{language_count}} languages</strong>.'),

    # Keep only if the model has the behaviours Contextable + Tree
    'deleting with N contexts and N children' => __('This item exists in <strong>{{context_count}} contexts</strong> and has <strong>{{children_count}} sub-items</strong>.'),
    'deleting with N contexts and 1 child' => __('This item exists in <strong>{{context_count}} contexts</strong> and has <strong>one sub-item</strong>.'),
    'deleting with N languages and N children' => __('This item exists in <strong>{{language_count}} languages</strong> and has <strong>{{children_count}} sub-items</strong>.'),
    'deleting with N languages and 1 child' => __('This item exists in <strong>{{language_count}} languages</strong> and has <strong>one sub-item</strong>.'),

    # Keep only if the model has the behaviour Tree
    'deleting with 1 child' => __('This item has <strong>1 sub-item</strong>.'),
    'deleting with N children' => __('This item has <strong>{{children_count}} sub-items</strong>.'),

    'deleting following contexts' => __('Delete this item in the following contexts:'),
    'deleting following languages' => __('Delete this item in the following languages:'),

    # Keep only if the model has the behaviour Tree
    'deleting with children following contexts' => __('Delete this item and all its sub-items in the following contexts:'),
    'deleting with children following languages' => __('Delete this item and all its sub-items in the following languages:'),

    // Appdesk: allLanguages
    'allLanguages' =>__('All'),
    'viewGrid' => __('Grid'),
    'viewTreeGrid' => __('Tree grid'),
    'viewThumbnails' => __('Thumbnails'),
    'preview' => __('Preview'),
    'loading' => _('Loading...'),
    'languages' => __('Languages'),
    'search' => __('Search'),
);