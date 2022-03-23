<?php

class media_plus extends rex_media
{
    private $profile = 'media_plus.'; // Präfix für Media-Manager Profil
    private $path = 'media/'; // Redaxo-Pfad zur Bilddatei (Standard: '/images/', benötigt YRewrite)
    private $class = 'media_plus'; // Class-Attribute Image-Elemente
    private $pictureClass = ''; // Class-Attribute für Picture-Elemente
    private $srcset = ''; // srcset-Attribut

    private $aspectratio = 0;
    private $loading = 'auto';

    public function __construct()
    {
        return $this;
    }

    public function setAutoProfiles($profile)
    {
        $this->profile = $profile;
        $media_manager_profiles = array_filter(rex_sql::factory()->setDebug(0)->getArray('SELECT * FROM `rex_media_manager_type` WHERE `name` LIKE :prefix', [':prefix' => $profile.'%']));
        $profiles = [];

        if (count($media_manager_profiles)) {
            foreach ($media_manager_profiles as $media_manager_profile) {
                $item = array_filter(explode('.', $media_manager_profile['name']));
                $profiles[$item[1]] = $media_manager_profile['name'];
            }
        } else {
            rex_view::warning('Es gibt keine passenden Media Manager Bildprofile. Bitte Profile im Schema `key.###px` verwenden.');
        }
        krsort($profiles, SORT_NUMERIC);
        $this->profiles = $profiles;

        return $this;
    }

    public function setSizes($sizes)
    {
        if (is_array($sizes)) {
            $this->sizes = $sizes;
        }

        return $this;
    }

    public function getSizes()
    {
        return $this->sizes;
    }

    public function setAutoCss($option)
    {
        $this->autoCss = $option;

        return $this;
    }

    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    public function getPath()
    {
        return $this->path;
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

    public function getClass()
    {
        return $this->class;
    }

    public function setPictureClass($class)
    {
        $this->pictureClass = $class;

        return $this;
    }

    public function getPictureClass()
    {
        return $this->pictureClass;
    }

    public function getStructuredData()
    {
        $fragment = new rex_fragment();
        $fragment->setVar('author', $this->getValue('med_copyright') ?? '');
        $fragment->setVar('location', $this->getValue('med_location') ?? '');
        $fragment->setVar('name', $this->getValue('name'));
        $fragment->setVar('datePublished', date('Y-m-d', $this->getUpdatedate()));
        $fragment->setVar('description', $this->getValue('med_description') ?? '');
        $fragment->setVar('title', $this->getTitle() ?? '');

        return html_entity_decode($fragment->parse('fragment/media_plus.php'));
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getSrcset()
    {
        if (is_array($this->profiles)) {
            $srcset_values = [];
            foreach ($this->profiles as $size => $profile) {
                $srcset_values[] = $this->path.$profile.'/'.$this->file.' '.$size;
            }
            $this->srcset = implode(',', $srcset_values);

            return $this->srcset;
        } else {
            return false;
        }
    }

    public function getImg()
    {
        $this->getSrcset();
        return '<img srcset="'.$this->srcset.'" src="'.$this->path.array_shift($this->profile)."/".$this->file.'" class="'.$this->class.'" alt="'.$this->title.'" sizes="'.$this->sizes.'" width="'.$this->getWidth().'" height="'.$this->getHeight().'" />';
    }

    public function getImgBase64()
    {
        return '<img src="data:image/'.$this->getType().';base64,'.base64_encode(rex_file::get(rex_path::media($this->name))).'" class="'.$this->class.'" alt="'.$this->title.'" width="'.$this->getWidth().'" height="'.$this->getHeight().'" />';
    }

    public function getSvg()
    {
        return "<!-- ".$this->name." -->".rex_file::get(rex_path::media($this->name));
    }

    public function getPicture()
    {
        if ('image/svg+xml' == $this->getType()) {
            return $this->getSvg();
        }

        $aspect_ratio = '';
        $style_img = '';

        if ($this->autoCss) {
            $media_cachefile = rex_media_manager::create($this->profile, $this->name)->getMedia();
            if ($media_cachefile instanceof rex_managed_media && $media_cachefile->getHeight() > 0) {
                $this->aspectratio = $media_cachefile->getWidth() / $media_cachefile->getHeight();
                $aspect_ratio = 'data-aspect-ratio="'. 100 / $this->aspectratio.'%"';
                $style_img = 'width: 100%;';
            }
        }

        $sources = [];
        foreach ($this->profiles as $resolution => $profile) {
            // Erstellen der Mediendatei
            $media_cachefile = rex_media_manager::create($profile, $this->name)->getMedia();
            if ($media_cachefile instanceof rex_managed_media) {
                $sources[] = '<source media="(min-width: '.$resolution.')" sizes="" type="image/'.$media_cachefile->getFormat().'" data-width="'.$media_cachefile->getWidth().'" data-height="'.$media_cachefile->getHeight().'" srcset="'.$this->path.$profile.'/'.$media_cachefile->getMediaFilename().'?timestamp='.filectime($media_cachefile->getSourcePath()).'">';
            }
        }

        $output = '';
        $output .= '<picture class="'.$this->pictureClass.'" '.$aspect_ratio.'>';
        $output .= implode('', $sources);

        $first_profile = reset($this->profiles);

        // Fallback-Bild für den Internet-Explorer immer die beste Auflösung.
        $media_cachefile = rex_media_manager::create($first_profile, $this->name)->getMedia();
        if ($media_cachefile instanceof rex_managed_media) {
            $output .= '<img  class="'.$this->class.'" loading="'.$this->loading.'" type="image/'.$media_cachefile->getFormat().'"  src="'.$this->path.$first_profile.'/'.$media_cachefile->getMediaFilename().'?timestamp='.filectime($media_cachefile->getSourcePath()).'" width="'.$media_cachefile->getWidth().'" height="'.$media_cachefile->getHeight().'" alt="'.$this->title.'" />';
        }
        $output .= '</picture>';

        return $output;
    }
}
