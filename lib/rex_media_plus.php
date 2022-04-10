<?php

class rex_media_plus extends rex_media
{
    private $loading = 'auto';

    public function __construct()
    {
        return $this;
    }

    public function getLoading()
    {
        return $this->loading;
    }

    public function setLoading($loading)
    {
        $this->loading = $loading;

        return $this;
    }

    public function getStructuredData()
    {
        $fragment = new rex_fragment();
        $fragment->setVar('title', $this->getTitle() ?? '');
        $fragment->setVar('description', $this->getValue('med_description') ?? '');
        $fragment->setVar('author', $this->getValue('med_copyright') ?? '');
        $fragment->setVar('location', $this->getValue('med_location') ?? '');
        $fragment->setVar('name', $this->getValue('name'));
        $fragment->setVar('datePublished', date('Y-m-d', $this->getUpdatedate()));

        return html_entity_decode($fragment->parse('media_plus/structured_data.php'));
    }

    public function getBackgroundStyles($group_name)
    {
        return media_manager_type_group::getByGroup($group_name)->getBackgroundStyles();
    }

    public function getImg($profile)
    {
        return '<img src="'.self::getFrontendUrl($this, $profile).'" alt="'.$this->getTitle().'" width="'.$this->getWidth().'" height="'.$this->getHeight().'" />';
    }

    public function getImgBase64()
    {
        return '<img src="data:image/'.$this->getType().';base64,'.base64_encode(rex_file::get(rex_path::media($this->name))).'" alt="'.$this->getTitle().'" width="'.$this->getWidth().'" height="'.$this->getHeight().'" />';
    }

    public function getSvg()
    {
        return
        "<!-- ".$this->name." -->".
        PHP_EOL.rex_file::get(rex_path::media($this->name)).PHP.EOL.
        "<!-- / ".$this->name." -->";
    }
    public static function getFrontendUrl($media, $profile = null, $show_timestamp = true)
    {
        $filename = $media;

        if ($media instanceof rex_media or $media instanceof rex_media_plus) {
            $filename = $media->getFileName();
        } elseif ($media instanceof rex_managed_media) {
            $filename = $media->getMediaFilename();
        }
        $timestamp = '';

        if ($show_timestamp) {
            if ($media instanceof rex_managed_media) {
                $timestamp = '?timestamp='.filectime($media->getSourcePath());
            } elseif ($media instanceof rex_media or $media instanceof rex_media_plus) {
                $timestamp = '?timestamp='.filectime(rex_path::media($filename));
            }
        }

        if ($profile) {
            return rex_url::media($profile. '/'. $filename) . $timestamp;
        }

        return rex_url::media($filename) . $timestamp;
    }
}
