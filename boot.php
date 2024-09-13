<?php

use Alexplusde\MediaManagerResponsive\Media;
use Alexplusde\MediaManagerResponsive\Type;
use Alexplusde\MediaManagerResponsive\TypeGroup;

if (rex_addon::get('yform')->isAvailable()) {
    rex_yform_manager_dataset::setModelClass(
        'rex_media_manager_type_group',
        TypeGroup::class,
    );
    rex_yform_manager_dataset::setModelClass(
        'rex_media_manager_type_meta',
        Type::class,
    );
}

// rex_extension::register('MEDIA_FORM_EDIT', array('rex_media_plus', 'mediapool_edit_svg_viewbox'));
rex_extension::register('MEDIA_FORM_ADD', Media::mediapool_updated_svg_viewbox(...));
// rex_extension::register('MEDIA_FORM_ADD', array('rex_media_plus', 'mediapool_add_svg_viewbox'));

if (rex_addon::get('cache_warmup')->isAvailable()) {
    if ('enhanced' === rex_config::get('media_manager_responsive', 'cache_warmup')) {
        rex_extension::register('PACKAGES_INCLUDED', static function () {
            rex_addon::get('cache_warmup')->setConfig('chunkSizePages', 1);
        });
        rex_extension::register('CACHE_WARMUP_IMAGES', static function (rex_extension_point $ep) {
            // $images = $ep->getSubject();
            return [];
        });
    }
}
