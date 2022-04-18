<?php

if (\rex_addon::get('yform') && \rex_addon::get('yform')->isAvailable()) {
    rex_yform_manager_dataset::setModelClass(
        'rex_media_manager_type_group',
        media_manager_type_group::class
    );
    rex_yform_manager_dataset::setModelClass(
        'rex_media_manager_type_meta',
        media_manager_type_meta::class
    );
}

// rex_extension::register('MEDIA_FORM_EDIT', array('rex_media_plus', 'mediapool_edit_svg_viewbox'));
rex_extension::register('MEDIA_UPDATED', array('rex_media_plus', 'mediapool_updated_svg_viewbox'));
// rex_extension::register('MEDIA_FORM_ADD', array('rex_media_plus', 'mediapool_add_svg_viewbox'));
