<?php

$addon = rex_addon::get('media_manager_responsive');

$table_name = 'rex_media_manager_type_group';

rex_extension::register(
    'YFORM_MANAGER_DATA_PAGE_HEADER',
    static function (rex_extension_point $ep) {
        $yform = $ep->getParam('yform');
        /** rex_yform_manager $yform */
        $table =  $yform->table;
        /** @var rex_yform_manager_table $table */
        if ($table->getTableName() === $ep->getParam('table_name')) {
            return '';
        }
    },
    rex_extension::EARLY,
    ['table_name' => $table_name],
);

$yform = $addon->getProperty('yform', []);
/** @var rex_yform_manager $yform */
$yform = $yform[rex_be_controller::getCurrentPage()] ?? [];

$_REQUEST['table_name'] = $table_name; /** @phpstan-ignore-line */

include rex_path::plugin('yform', 'manager', 'pages/data_edit.php');
