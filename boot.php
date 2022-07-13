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

if(\rex_addon::get('cache_warmup') && \rex_addon::get('cache_warmup')->isAvailable()) {
    if(rex_config::get('media_manager_responsive', 'cache_warmup') == "enhanced") {
        rex_extension::register('PACKAGES_INCLUDED', function () {   
            rex_addon::get('cache_warmup')->setConfig('chunkSizePages', 1);
        });
        rex_extension::register('CACHE_WARMUP_IMAGES', function (rex_extension_point $ep) {
            // $images = $ep->getSubject();
            return [];
        });
    }
}
