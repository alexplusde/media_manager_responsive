<?php

// Alle Medientypen bei Uninstall exportieren. Nur zu Test- und Entwicklungszwecken
/*
$mediaTypes = rex_sql::factory()->setDebug(0)->getArray("SELECT * FROM rex_media_manager_type WHERE `name` LIKE '%media_plus.%'");

rex_file::put(rex_path::addon("media_plus", "install/rex_media_manager_type.json"), json_encode($mediaTypes));

$type_ids = [];
foreach ($mediaTypes as $mediaType) {
    $type_ids[] = $mediaType['id'];
}
$mediaEffects = rex_sql::factory()->setDebug(0)->getArray("SELECT E.effect, E.parameters, E.priority, T.name FROM (SELECT * FROM rex_media_manager_type_effect) E LEFT JOIN (SELECT `id`, `name` FROM rex_media_manager_type) T ON T.id = E.type_id WHERE FIND_IN_SET(`E`.`type_id`, :type_ids)", [":type_ids" => implode(",", $type_ids)]);

rex_file::put(rex_path::addon("media_plus", "install/rex_media_manager_type_effects.json"), json_encode($mediaEffects));
*/
