<?php

$addon = rex_addon::get('media_manager_responsive');

include_once __DIR__ . '/install/update_schema.php';

if (rex_addon::get('yform')->isAvailable()) {
    $file = rex_file::get(rex_path::addon($addon->getName(), 'install/media_manager_responsive.tableset.json'));
    if (null !== $file) {
        rex_yform_manager_table_api::importTablesets($file);
        rex_yform_manager_table::deleteCache();
    }
}

/*
$mediaTypes = json_decode(rex_file::get(rex_path::addon("media_plus")."install/rex_media_manager_type.json"), 1);

foreach ($mediaTypes as $mediaType) {
    $sql = rex_sql::factory()->setDebug(0)->setTable("rex_media_manager_type");

    foreach ($mediaType as $key => $value) {
        if ($key == "id") {
            continue;
        }
        $sql->setValue($key, $value);
    }

    $sql->insertOrUpdate();
}

$media_types = rex_sql::factory()->setDebug(0)->getArray('SELECT id, name FROM rex_media_manager_type WHERE `name` LIKE "%media_plus.%"');
$media_type_keys = [];
foreach ($media_types as $media_type) {
    $media_type_keys[$media_type['name']] = $media_type['id'];
}

$mediaEffects = json_decode(rex_file::get(rex_path::addon("media_plus")."install/rex_media_manager_type_effects.json"), 1);

foreach ($mediaEffects as $mediaEffect) {
    $sql = rex_sql::factory()->setDebug(0)->setTable("rex_media_manager_type_effect");
    foreach ($mediaEffect as $key => $value) {
        if ($key == "id") {
            continue;
        }
        $sql->setValue($key, $value);
    }
    $sql->setValue("type_id", $media_type_keys[$mediaEffect['name']]);

    $sql->insertOrUpdate();
}
*/
