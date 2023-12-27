<?php
class media_manager_type_group extends \rex_yform_manager_dataset
{
    private array $types = [];
    private array $profiles = [];
    private array $group = [];
    private string $media_path = "media";

    public static function getByGroup(string $group_name): ?media_manager_type_group
    {
        return self::query()->where('name', $group_name)->findOne();
    }
    private function getFallback() :int
    {
        return $this->getValue('fallback_id');
    }

    /** @api */
    private function getMeta() :string
    {
        return $this->getValue('meta');
    }
    private static function getTypes(string $group_name = ""): ?rex_yform_manager_collection
    {
        if($group_name) {
            $group = self::getByGroup($group_name);
            return media_manager_type_meta::query()->where('group_id', $group->getId())->orderBy('prio')->find();
        }
        return media_manager_type_meta::query()->orderBy('prio')->find();
    }

    private static function getMediaCacheFile(string $type, string $file): ?rex_managed_media
    {
        return rex_media_manager::create($type, $file)->getMedia();
    }

    public static function getPicture(string $groupname, rex_media_plus $media_plus) :string
    {
        if ($media_plus) {
            $file = $media_plus->getFilename();
            if ('image/svg+xml' == $media_plus->getType()) {
                return $media_plus->getSvg();
            }

            $picture[] = '<picture data-group-profile="'.$groupname.'">';
            $group = self::getByGroup($groupname);
            $types = self::getTypes($groupname);

            foreach ($types as $type) {
                /** @var $type media_manager_type_meta **/
                // Erstellen der Mediendatei
                $cached_media = self::getMediaCacheFile($type->getType(), $file);
                if ($cached_media instanceof rex_managed_media) {
                    $media['devices'] = "all";
                    if ($type->getMinWidth() != "") {
                        $media['min_width'] = '(min-width: '.$type->getMinWidth().')';
                    }
                    if ($type->getMaxWidth() != "") {
                        $media['max_width'] = '(max-width: '.$type->getMaxWidth().')';
                    }
                    $media_attr = implode(" AND ", $media);
                    $picture[] = '<source media="'.$media_attr.'" sizes="" type="image/'.$cached_media->getFormat().'" data-width="'.$cached_media->getWidth().'" data-height="'.$cached_media->getHeight().'" srcset="'.rex_media_plus::getFrontendUrl($cached_media, $type->getType(), $file).'">';
                }
            }

            $cached_media = self::getMediaCacheFile($group->getFallback(), $file);

            if ($cached_media instanceof rex_managed_media) {
                $picture[] = '<img '.implode(' ', $media_plus->getAttributes()).' class="'.$media_plus->getClass().'" type="image/'.$cached_media->getFormat().'" src="'.rex_media_plus::getFrontendUrl($cached_media, $group->getFallback(), true).'" width="'.$cached_media->getWidth().'" height="'.$cached_media->getHeight().'" alt="'.$media_plus->getTitle().'" />';
            }
    
            $picture[] = '</picture>';
            return implode('', $picture);
        }
    }

    
    private function getSrcset(rex_media_plus $media_plus) :string
    {
        if ($media_plus instanceof rex_media_plus) {
            /** @var $type media_manager_type_meta **/
            $types = $this->getTypes();

            foreach ($types as $type) {
                $srcset[] = rex_media_plus::getFrontendUrl($media_plus, $type->getType()).' '.$type->getMinWidth();
            }

            return implode(',', $srcset);
        }
    }

    public function getImg(string $file) :string
    {
        $media_plus = rex_media_plus::get($file);
        return '<img srcset="'.$this->getSrcset($media_plus).'" src="'.rex_media_plus::getFrontendUrl($managed_media, $type->getType(), $file).'" width="'.$media_plus->getWidth().'" height="'.$media_plus->getHeight().'" />';
    }
    
    public static function getBackgroundStyles(string $file, string $groupname, string $selector, string $fragment_path = 'media_manager_responsive/background_styles.php') :string
    {
        $media_plus = rex_media_plus::get($file);

        if (!$media_plus) {
            return "";
        }
        
        if ('image/svg+xml' == $media_plus->getType()) {
            return $media_plus->getSvg();
        }
        
        $group = self::getByGroup($groupname);
        $types = self::getTypes($groupname);
        $querys = [];
        foreach ($types as $type) {
            /** @var $type media_manager_type_meta **/
            // Erstellen der Mediendatei
            $cached_media = rex_media_manager::create($type->getType(), $file)->getMedia();
            if ($cached_media instanceof rex_managed_media) {
                $media['devices'] = "all";
                if ($type->getMinWidth() != "") {
                    $media['min_width'] = '(min-width: '.$type->getMinWidth().')';
                }
                if ($type->getMaxWidth() != "") {
                    $media['max_width'] = '(max-width: '.$type->getMaxWidth().')';
                }
                $media_attr = implode(" and ", $media);
                $querys[] = '@media '.$media_attr .' { '.$selector.'{ background-image: url('.rex_media_plus::getFrontendUrl($cached_media, $type->getType(), $file).');} }';
            }
        }
        
        $fragment = new rex_fragment();
        $fragment->setVar('file', $file);
        $fragment->setVar('querys', $querys);

        return html_entity_decode($fragment->parse($fragment_path));
    }
}
