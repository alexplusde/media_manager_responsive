<?php

if (\rex_addon::get('yform') && \rex_addon::get('yform')->isAvailable()) {
    rex_yform_manager_dataset::setModelClass(
        'rex_media_manager_type_group',
        media_manager_type_group::class
    );
    rex_yform_manager_dataset::setModelClass(
        'rex_media_manger_type_meta',
        media_manager_type_meta::class
    );
}
