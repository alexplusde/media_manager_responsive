<?php

class rex_media_plus extends rex_media
{
    public $loading = 'auto';
    public $class = "";
    public $attributes = [];

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

    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }
    public function getClass() :string
    {
        return $this->class;
    }
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }
    public function getAttributes()
    {
        if ($this->attributes === null) {
            return [];
        }
        return $this->attributes;
    }

    public function getStructuredData() :string
    {
        $fragment = new rex_fragment();
        $fragment->setVar('title', $this->getTitle() ?? '');
        $fragment->setVar('description', $this->getValue('med_description') ?? '');
        $fragment->setVar('author', $this->getValue('med_copyright') ?? '');
        $fragment->setVar('location', $this->getValue('med_location') ?? '');
        $fragment->setVar('name', $this->getValue('name'));
        $fragment->setVar('datePublished', date('Y-m-d', $this->getUpdatedate()));

        return html_entity_decode($fragment->parse('media_manager_responsive/structured_data.php'));
    }

    public function getBackgroundStyles($group_name) :string
    {
        return media_manager_type_group::getByGroup($group_name)->getBackgroundStyles();
    }

    public function getImg($profile = null)
    {
        if ($profile) {
            return '<img '.implode(' ', $this->getAttributes()).' class="'.$this->getClass().'" src="'.self::getFrontendUrl($this, $profile).'" alt="'.$this->getTitle().'" width="'.$this->getWidth().'" height="'.$this->getHeight().'" />';
        }
        return '<img '.implode(' ', $this->getAttributes()).' class="'.$this->getClass().'" src="'.self::getFrontendUrl($this).'" alt="'.$this->getTitle().'" width="'.$this->getWidth().'" height="'.$this->getHeight().'" />';
    }

    public function getImgBase64($only_data = false)
    {
        $data = 'data:image/'.$this->getType().';base64,'.base64_encode(rex_file::get(rex_path::media($this->name)));
        if ($only_data) {
            return $data;
        }
        return '<img '.implode(' ', $this->getAttributes()).' class="'.$this->getClass().'" src="'.$data.'" alt="'.$this->getTitle().'" width="'.$this->getWidth().'" height="'.$this->getHeight().'" />';
    }

    public function getSvg()
    {
        return
        "<!-- ".$this->name." -->".
        PHP_EOL.rex_file::get(rex_path::media($this->name)).PHP.EOL.
        "<!-- / ".$this->name." -->";
    }

    public function getPicture($groupname)
    {
        return media_manager_type_group::getPicture($groupname, $this);
    }

    public static function getFrontendUrl($media, $profile = null, $show_timestamp = true)
    {
        if ($media instanceof rex_media or $media instanceof rex_media_plus) {
            $filename = $media->getFileName();
        } elseif ($media instanceof rex_managed_media) {
            # $filename = $media->getMediaFilename();
            # Workaround wg. https://github.com/redaxo/redaxo/issues/4519#issuecomment-1183515367
            $path = explode(DIRECTORY_SEPARATOR, $media->getMediaPath());
            $filename = array_pop($path);
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

    public static function mediapool_updated_svg_viewbox(rex_extension_point $ep)
    {
        $subject = $ep->getSubject();
        $filename = $ep->getParam('filename');

        $media = rex_media::get($filename);

        if ('image/svg+xml' == $media->getType()) {
            $xml = simplexml_load_file(rex_path::media($filename));


            $viewBox = $xml['viewBox'] ? $xml['viewBox']->__toString() : 0;
            $viewBox = preg_split('/[\s,]+/', $viewBox);
            $width = (float) ($viewBox[2] - $viewBox[0] ?? 0);
            $height = (float) ($viewBox[3] - $viewBox[1] ?? 0);

            if (!$height && !$width) {
                $width = $xml['width'] ? $xml['width']->__toString() : 0;
                $height = $xml['height'] ? $xml['height']->__toString() : 0;
            }

            $sql = rex_sql::factory();
            $sql->setWhere('filename="'.$filename.'"');
            $sql->setTable('rex_media');
            $sql->setValue('width', $width);
            $sql->setValue('height', $height);
            $sql->update();
        }

        return $subject;
    }
}
