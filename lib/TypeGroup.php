<?php

namespace Alexplusde\MediaManagerResponsive;

use InvalidArgumentException;
use BadMethodCallException;
use rex_exception;
use rex_managed_media;
use rex_media_manager;
use rex_yform_manager_dataset;
use rex_yform_manager_collection;
use rex_fragment;
use rex_sql_exception;

class TypeGroup extends rex_yform_manager_dataset
{
    /**
     * @api
     * @param string $group_name 
     * @return null|TypeGroup 
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
     * @param string $group_name 
     * @return null|rex_yform_manager_collection<Type> 
     */
    public static function getTypes(string $group_name = ''): ?rex_yform_manager_collection
    {
        if ($group_name === '') {
            return Type::query()->orderBy('prio')->find();
        }
        $group = self::getByGroup($group_name);
        if ($group === null) {
            return null;
        }
        return Type::query()->where('group_id', $group->getId())->orderBy('prio')->find();

    }

    /**
     * @api
     * @param string $type 
     * @param string $file 
     * @return rex_managed_media 
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

        if($types === null) {
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

        if($group !== null) {
            $cached_media = self::getMediaCacheFile($group->getFallback(), $file);
            $picture[] = '<img ' . implode(' ', $media->getAttributes()) . ' class="' . $media->getClass() . '" type="image/' . $cached_media->getFormat() . '" src="' . Media::getFrontendUrl($cached_media, $group->getFallback(), true) . '" width="' . $cached_media->getWidth() . '" height="' . $cached_media->getHeight() . '" alt="' . $media->getTitle() . '" />';
        }

        $picture[] = '</picture>';
        return implode('', $picture);

    }

    public function getSrcset(Media $media_plus, string $group = ''): string
    {
        $types = self::getTypes($group);
        if($types === null) {
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
     * @param string $file 
     * @param string $type 
     * @return string 
     */
    public function getImg(string $file, string $type = ''): string
    {
        $media = Media::get($file);
        if($media !== null) {
            return '<img srcset="' . $this->getSrcset($media) . '" src="' . Media::getFrontendUrl($media, $type, true) . '" width="' . $media->getWidth() . '" height="' . $media->getHeight() . '" />';
        }
        return '<!-- file "'.$file.'" does not exist -->';
    }

    /**
     * @api
     * @param string $file 
     * @param string $groupname 
     * @param string $selector 
     * @param string $fragment_path 
     * @return string 
     */
    public static function getBackgroundStyles(string $file, string $groupname, string $selector, string $fragment_path = 'media_manager_responsive/background_styles.php'): string
    {
        $media = Media::get($file);

        if ($media === null) {
            /* TODO: Log this case */
            return '';
        }

        if ('image/svg+xml' === $media->getType()) {
            return $media->getSvg();
        }

        $types = self::getTypes($groupname);
        $querys = [];
        if($types === null) {
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
