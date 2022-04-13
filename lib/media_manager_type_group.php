<?php
class media_manager_type_group extends \rex_yform_manager_dataset
{
    private $types = [];
    private $profiles = [];
    private $group = [];
    private $media_path = "media";

    public static function getByGroup($group_name)
    {
        return self::query()->where('name', $group_name)->findOne();
    }
    private function getFallback()
    {
        return $this->getValue('fallback_id');
    }

    private function getMeta()
    {
        return $this->getValue('meta');
    }
    private static function getTypes($group_name)
    {
        $group = self::getByGroup($group_name);
        return media_manager_type_meta::query()->where('group_id', $group->getId())->orderBy('prio')->find();
    }

    private static function getMediaCacheFile($type, $file)
    {
        return rex_media_manager::create($type, $file)->getMedia();
    }

    public static function getPicture($groupname, $file)
    {
        $media_plus = rex_media_plus::get($file);
        if ($media_plus) {
            if ('image/svg+xml' == $media_plus->getType()) {
                return $media_plus->getSvg();
            }

            $picture[] = '<picture>';
            $group = self::getByGroup($groupname);
            $types = self::getTypes($groupname);

            foreach ($types as $type) {
                // Erstellen der Mediendatei
                $cached_media = self::getMediaCacheFile($type->getType(), $file);
                if ($cached_media instanceof rex_managed_media) {
                    $picture[] = '<source media="(min-width: '.$type->getMinWidth().')" sizes="" type="image/'.$cached_media->getFormat().'" data-width="'.$cached_media->getWidth().'" data-height="'.$cached_media->getHeight().'" srcset="'.rex_media_plus::getFrontendUrl($cached_media, $type->getType(), $file).'">';
                }
            }

            $cached_media = self::getMediaCacheFile($group->getFallback(), $file);

            if ($cached_media instanceof rex_managed_media) {
                $picture[] = '<img style="width: 100%; height: auto;" type="image/'.$cached_media->getFormat().'" src="'.rex_media_plus::getFrontendUrl($cached_media, $group->getFallback(), $file).'" width="'.$cached_media->getWidth().'" height="'.$cached_media->getHeight().'" alt="'.$media_plus->getTitle().'" />';
            }
    
            $picture[] = '</picture>';
            return implode('', $picture);
        }
    }

    
    private function getSrcset($media_plus)
    {
        if ($media_plus instanceof media_plus) {
            $types = $this->getTypes();

            foreach ($types as $type) {
                $srcset[] = rex_media_plus::getFrontendUrl($media_plus, $type->getType(), $file).' '.$type->getMinWidth();
            }

            return implode(',', $srcset);
        }
    }

    public function getImg($file)
    {
        $media_plus = media_plus::get($file);
        return '<img srcset="'.$this->getSrcset($media_plus).'" src="'.rex_media_plus::getFrontendUrl($managed_media, $type->getType(), $file).'" width="'.$media_plus->getWidth().'" height="'.$media_plus->getHeight().'" />';
    }
    
    public static function getBackgroundStyles($file, $groupname, $selector, $fragment_path = 'media_manager_responsive/background_styles.php')
    {
        $media_plus = rex_media_plus::get($file);

        if (!$media_plus) {
            return;
        }
        
        if ('image/svg+xml' == $media_plus->getType()) {
            return $media_plus->getSvg();
        }
        
        $group = self::getByGroup($groupname);
        $types = self::getTypes($groupname);
        $querys = [];
        foreach ($types as $type) {
            // Erstellen der Mediendatei
            $cached_media = rex_media_manager::create($type->getType(), $file)->getMedia();
            if ($cached_media instanceof rex_managed_media) {
                $querys[] = '@media(min-width: '.$type->getMinWidth().') { '.$selector.'{ background-image: url('.rex_media_plus::getFrontendUrl($cached_media, $type->getType(), $file).');} }';
            }
        }
        
        $fragment = new rex_fragment();
        $fragment->setVar('file', $file);
        $fragment->setVar('querys', $querys);

        return html_entity_decode($fragment->parse($fragment_path));
    }
}
