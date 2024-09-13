<?php

namespace Alexplusde\MediaManagerResponsive;

use rex_fragment;
use rex_managed_media;
use rex_media_manager;
use rex_yform_manager_collection;
use rex_yform_manager_dataset;

class TypeGroup extends rex_yform_manager_dataset
{
    /**
     * @api
     */
    public static function getByGroup(string $group_name): ?self
    {
        return self::query()->where('name', $group_name)->findOne();
    }

    /** @api */
    public function getFallback(): string
    {
        return $this->getValue('fallback_id');
    }

    /** @api */
    public function getMeta(): mixed
    {
        return $this->getValue('meta');
    }

    /**
     * @api
     * @return rex_yform_manager_collection<Type>|null
     */
    public static function getTypes(string $group_name = ''): ?rex_yform_manager_collection
    {
        if ('' === $group_name) {
            return Type::query()->orderBy('prio')->find();
        }
        $group = self::getByGroup($group_name);
        if (null === $group) {
            return null;
        }
        return Type::query()->where('group_id', $group->getId())->orderBy('prio')->find();
    }

    /**
     * @api
     */
    public static function getMediaCacheFile(string $type, string $file): rex_managed_media
    {
        return rex_media_manager::create($type, $file)->getMedia();
    }

    public static function getPicture(string $groupname, Media $media): string
    {
        $file = $media->getFileName();
        if ('image/svg+xml' === $media->getType()) {
            return $media->getSvg();
        }

        $picture = [];
        $picture[] = '<picture data-group-profile="' . $groupname . '">';
        $group = self::getByGroup($groupname);
        $types = self::getTypes($groupname);

        if (null === $types) {
            /** TODO: Log this case */
            return '';
        }

        foreach ($types as $type) {
            /** @var Type $type */
            // Erstellen der Mediendatei
            $cached_media = self::getMediaCacheFile($type->getType(), $file);
            $attributes = [];
            $attributes['devices'] = 'all';
            if ('' !== $type->getMinWidth()) {
                $attributes['min_width'] = '(min-width: ' . $type->getMinWidth() . ')';
            }
            if ('' !== $type->getMaxWidth()) {
                $attributes['max_width'] = '(max-width: ' . $type->getMaxWidth() . ')';
            }
            $media_attr = implode(' AND ', $attributes);
            $picture[] = '<source media="' . $media_attr . '" sizes="" type="image/' . $cached_media->getFormat() . '" data-width="' . $cached_media->getWidth() . '" data-height="' . $cached_media->getHeight() . '" srcset="' . Media::getFrontendUrl($cached_media, $type->getType(), true) . '">';
        }

        if (null !== $group) {
            $cached_media = self::getMediaCacheFile($group->getFallback(), $file);
            $picture[] = '<img ' . implode(' ', $media->getAttributes()) . ' class="' . $media->getClass() . '" type="image/' . $cached_media->getFormat() . '" src="' . Media::getFrontendUrl($cached_media, $group->getFallback(), true) . '" width="' . $cached_media->getWidth() . '" height="' . $cached_media->getHeight() . '" alt="' . $media->getTitle() . '" />';
        }

        $picture[] = '</picture>';
        return implode('', $picture);
    }

    public function getSrcset(Media $media_plus, string $group = ''): string
    {
        $types = self::getTypes($group);
        if (null === $types) {
            /** TODO: Log this case */
            return '';
        }
        $srcset = [];
        foreach ($types as $type) {
            /** @var Type $type */
            $srcset[] = Media::getFrontendUrl($media_plus, $type->getType()) . ' ' . $type->getMinWidth();
        }

        return implode(',', $srcset);
    }

    /**
     * @api
     */
    public function getImg(string $file, string $type = ''): string
    {
        $media = Media::get($file);
        if (null !== $media) {
            return '<img srcset="' . $this->getSrcset($media) . '" src="' . Media::getFrontendUrl($media, $type, true) . '" width="' . $media->getWidth() . '" height="' . $media->getHeight() . '" />';
        }
        return '<!-- file "' . $file . '" does not exist -->';
    }

    /**
     * @api
     */
    public static function getBackgroundStyles(string $file, string $groupname, string $selector, string $fragment_path = 'media_manager_responsive/background_styles.php'): string
    {
        $media = Media::get($file);

        if (null === $media) {
            /* TODO: Log this case */
            return '';
        }

        if ('image/svg+xml' === $media->getType()) {
            return $media->getSvg();
        }

        $types = self::getTypes($groupname);
        $querys = [];
        if (null === $types) {
            /* TODO: Log this case */
            return '';
        }
        foreach ($types as $type) {
            /** @var Type $type */
            // Erstellen der Mediendatei
            $cached_media = rex_media_manager::create($type->getType(), $file)->getMedia();

            $attributes = [];
            $attributes['devices'] = 'all';
            if ('' !== $type->getMinWidth()) {
                $attributes['min_width'] = '(min-width: ' . $type->getMinWidth() . ')';
            }
            if ('' !== $type->getMaxWidth()) {
                $attributes['max_width'] = '(max-width: ' . $type->getMaxWidth() . ')';
            }
            $media_attr = implode(' and ', $attributes);
            $querys[] = '@media ' . $media_attr . ' { ' . $selector . '{ background-image: url(' . Media::getFrontendUrl($cached_media, $type->getType(), true) . ');} }';
        }

        $fragment = new rex_fragment();
        $fragment->setVar('file', $file);
        $fragment->setVar('querys', $querys);

        return html_entity_decode($fragment->parse($fragment_path));
    }
}
