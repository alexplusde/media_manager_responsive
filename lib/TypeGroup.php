<?php

namespace Alexplusde\MediaManagerResponsive;

use BadMethodCallException;
use InvalidArgumentException;
use rex_exception;
use rex_managed_media;
use rex_media_manager;
use rex_yform_manager_dataset;
use rex_yform_manager_collection;
use rex_fragment;
use rex_sql_exception;
use RuntimeException;

class TypeGroup extends rex_yform_manager_dataset
{
    public static function getByGroup(string $group_name): ?self
    {
        return self::query()->where('name', $group_name)->findOne();
    }

    /** @api */
    private function getFallback(): string
    {
        return $this->getValue('fallback_id');
    }

    /** @api */
    private function getMeta(): string
    {
        return $this->getValue('meta');
    }

    private static function getTypes(string $group_name = ''): ?rex_yform_manager_collection
    {
        if ($group_name) {
            $group = self::getByGroup($group_name);
            return Type::query()->where('group_id', $group->getId())->orderBy('prio')->find();
        }
        return Type::query()->orderBy('prio')->find();
    }

    private static function getMediaCacheFile(string $type, string $file): rex_managed_media
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

        foreach ($types as $type) {
            /** @var Type $type */
            // Erstellen der Mediendatei
            $cached_media = self::getMediaCacheFile($type->getType(), $file);
            if ($cached_media instanceof rex_managed_media) {
                $attributes = [];
                $attributes['devices'] = 'all';
                if ('' !== $type->getMinWidth()) {
                    $attributes['min_width'] = '(min-width: ' . $type->getMinWidth() . ')';
                }
                if ('' !== $type->getMaxWidth()) {
                    $attributes['max_width'] = '(max-width: ' . $type->getMaxWidth() . ')';
                }
                $media_attr = implode(' AND ', $attributes);
                $picture[] = '<source media="' . $media_attr . '" sizes="" type="image/' . $cached_media->getFormat() . '" data-width="' . $cached_media->getWidth() . '" data-height="' . $cached_media->getHeight() . '" srcset="' . Media::getFrontendUrl($cached_media, $type->getType(), $file) . '">';
            }
        }

        $cached_media = self::getMediaCacheFile($group->getFallback(), $file);

        if ($cached_media instanceof rex_managed_media) {
            $picture[] = '<img ' . implode(' ', $media->getAttributes()) . ' class="' . $media->getClass() . '" type="image/' . $cached_media->getFormat() . '" src="' . Media::getFrontendUrl($cached_media, $group->getFallback(), true) . '" width="' . $cached_media->getWidth() . '" height="' . $cached_media->getHeight() . '" alt="' . $media->getTitle() . '" />';
        }

        $picture[] = '</picture>';
        return implode('', $picture);

    }

    private function getSrcset(Media $media_plus): string
    {
        if ($media_plus instanceof Media) {
            $types = $this->getTypes();
            $srcset = [];
            foreach ($types as $type) {
                /** @var Type $type */
                $srcset[] = Media::getFrontendUrl($media_plus, $type->getType()) . ' ' . $type->getMinWidth();
            }

            return implode(',', $srcset);
        }
    }

    /**
     * @api
     * @param string $file 
     * @param string $type 
     * @return string 
     * @throws rex_exception 
     * @throws rex_sql_exception 
     */
    public function getImg(string $file, string $type = ''): string
    {
        $media_plus = Media::get($file);
        return '<img srcset="' . $this->getSrcset($media_plus) . '" src="' . Media::getFrontendUrl($media_plus, $type, true) . '" width="' . $media_plus->getWidth() . '" height="' . $media_plus->getHeight() . '" />';
    }

    /**
     * @api
     * @param string $file 
     * @param string $groupname 
     * @param string $selector 
     * @param string $fragment_path 
     * @return string 
     * @throws RuntimeException 
     * @throws rex_sql_exception 
     * @throws rex_exception 
     * @throws BadMethodCallException 
     * @throws InvalidArgumentException 
     */
    public static function getBackgroundStyles(string $file, string $groupname, string $selector, string $fragment_path = 'media_manager_responsive/background_styles.php'): string
    {
        $media = Media::get($file);

        if (!$media) {
            return '';
        }

        if ('image/svg+xml' === $media->getType()) {
            return $media->getSvg();
        }

        $types = self::getTypes($groupname);
        $querys = [];
        foreach ($types as $type) {
            /** @var media_manager_type_meta $type */
            // Erstellen der Mediendatei
            $cached_media = rex_media_manager::create($type->getType(), $file)->getMedia();
            if ($cached_media instanceof rex_managed_media) {
                $attributes = [];
                $attributes['devices'] = 'all';
                if ('' !== $type->getMinWidth()) {
                    $attributes['min_width'] = '(min-width: ' . $type->getMinWidth() . ')';
                }
                if ('' !== $type->getMaxWidth()) {
                    $attributes['max_width'] = '(max-width: ' . $type->getMaxWidth() . ')';
                }
                $media_attr = implode(' and ', $attributes);
                $querys[] = '@media ' . $media_attr . ' { ' . $selector . '{ background-image: url(' . Media::getFrontendUrl($cached_media, $type->getType(), $file) . ');} }';
            }
        }

        $fragment = new rex_fragment();
        $fragment->setVar('file', $file);
        $fragment->setVar('querys', $querys);

        return html_entity_decode($fragment->parse($fragment_path));
    }
}
