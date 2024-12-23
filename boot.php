<?php

use Alexplusde\MediaManagerResponsive\Media;
use Alexplusde\MediaManagerResponsive\Type;
use Alexplusde\MediaManagerResponsive\TypeGroup;
use Alexplusde\MediaManagerResponsive\TypeInjector;

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

if (rex::isFrontend() && '' != rex_config::get('media_manager_responsive', 'auto_inject_type')) {
    rex_extension::register('OUTPUT_FILTER', static function (rex_extension_point $ep) {
        $html = $ep->getSubject();
        return TypeInjector::injectType($html, rex_config::get('media_manager_responsive', 'auto_inject_type'));
    });
}


// Wenn Backend-Seite media_manager_types, dann in REDAXOs Datelist-EP einhängen

if (rex::isBackend() && rex_be_controller::getCurrentPage() == 'media_manager/types') {
    rex_extension::register('REX_LIST_GET', function (rex_extension_point $ep) {
        /** @var rex_list $subject */
        $subject = $ep->getSubject();

        $media = Media::get('media_manager_responsive_preview_image.jpg');

        if($media) {
            // Liste um Bild erweitern
            $subject->addColumn('preview', 'Vorschau');
            $subject->setColumnPosition('preview', 2);
            $subject->setColumnLabel('preview', 'Vorschau');
            $subject->setColumnFormat('preview', 'custom', function ($params) use ($media) {
                $list = $params['list'];
                $type = $list->getValue('name');
                $rex_managed_media = \rex_media_manager::create($type, $media->getFilename())->getMedia();

                $height = $rex_managed_media->getHeight() ?? 0;
                $width = $rex_managed_media->getWidth() ?? 0;
                $imagetype = $rex_managed_media->getFormat() ?? "image/jpeg";

                $return = '<a href="/media/' . $type ."/". $media->getFileName() . '" target="_blank"><img src="/media/' . $type ."/". $media->getFileName() . '" style="max-width: 120px; max-height: 120px;"></a>';
                $return .= '<span class="badge badge-primary">' . $width . 'px ✕ '. $height . 'px</span>';
                $return .= ' <span class="badge badge-secondary">' . $imagetype . '</span>';
                return $return;
            });
        }

        return $subject;
    });
}
