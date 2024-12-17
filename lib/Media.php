<?php

namespace Alexplusde\MediaManagerResponsive;

use rex_extension_point;
use rex_file;
use rex_fragment;
use rex_managed_media;
use rex_media;
use rex_media_manager;
use rex_path;
use rex_sql;
use rex_string;
use rex_url;

use const DIRECTORY_SEPARATOR;
use const PHP_EOL;

class Media extends rex_media
{
    /** @api */
    public string $loading = 'auto';
    /** @api */
    public string $class = '';
    /**
     * @var array<string>
     * @api */
    public array $plus_attributes = [];

    public function __construct()
    {
        $this->plus_attributes = [];
    }

    /**
     * @api
     */
    public function getLoading(): string
    {
        return $this->loading;
    }

    /**
     * @api
     */
    public function setLoading(string $loading): self
    {
        $this->loading = $loading;

        return $this;
    }

    /**
     * @api
     */
    public function setClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @api
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @api
     * @param array<string> $attributes
     */
    public function setAttributes(array $attributes): self
    {
        $this->plus_attributes = $attributes;

        return $this;
    }

    /**
     * @api
     * @return array<string>
     */
    public function getAttributes(): array
    {
        return $this->plus_attributes ?? [];
    }

    /**
     * @api
     */
    public function getStructuredData(): string
    {
        $fragment = new rex_fragment();
        $fragment->setVar('title', $this->getTitle());
        $fragment->setVar('description', $this->getValue('med_description'));
        $fragment->setVar('author', $this->getValue('med_copyright') ?? '');
        $fragment->setVar('location', $this->getValue('med_location') ?? '');
        $fragment->setVar('name', $this->getValue('name'));
        $fragment->setVar('datePublished', date('Y-m-d', $this->getUpdateDate()));

        return html_entity_decode($fragment->parse('media_manager_responsive/structured_data.php'));
    }

    /**
     * @api
     * @return string
     */
    public function getBackgroundStyles(string $group_name, string $selector)
    {
        return TypeGroup::getBackgroundStyles($this->getFileName(), $group_name, $selector);
    }

    /**
     * @api
     */
    public function getImg(string $type = ''): string
    {
        $img = $this->getImgAsAttributesArray($type);
        $attributes = rex_string::buildAttributes($img);
        return '<img ' . $attributes . ' />';
    }
    

    /**
     * @api
     */
    public function getImgAsAttributesArray(string $type = ''): array
    {
        
        if($this->getType() === 'image/svg+xml') {
            $type = '';
        }
        $img = $this->getAttributes();
        $img['src'] = self::getFrontendUrl($this, $type);
        $managed_media = $this->getMediaCacheFile($type);

        if ('' !== $type & $managed_media instanceof rex_managed_media && $managed_media->getWidth() > 0 && $managed_media->getHeight() > 0) {
            $img['width'] = $managed_media->getWidth();
            $img['height'] = $managed_media->getHeight();
        } else if($this->getWidth() > 0 && $this->getHeight() > 0) {
            $img['width'] = $this->getWidth();
            $img['height'] = $this->getHeight();
        }
        $img['alt'] = $this->getTitle();
        $img['class'] = $this->getClass();
        $img['loading'] = $this->getLoading();

        return $img;
    }

    /**
     * @api
     */
    public function getMediaCacheFile(string $type): ?rex_managed_media
    {
        $file = $this->getFilename();
        return rex_media_manager::create($type, $file)->getMedia();
    }

    /**
     * @api
     */
    public function getImgBase64(bool $only_data = false): string
    {
        $data = 'data:image/' . $this->getType() . ';base64,' . base64_encode(rex_file::get(rex_path::media($this->name)) ?? '');
        if ($only_data) {
            return $data;
        }
        return '<img ' . implode(' ', $this->getAttributes()) . ' class="' . $this->getClass() . '" src="' . $data . '" alt="' . $this->getTitle() . '" width="' . $this->getWidth() . '" height="' . $this->getHeight() . '" />';
    }

    /**
     * @api
     */
    public function getSvg(): string
    {
        return
        '<!-- ' . $this->name . ' -->' .
        PHP_EOL . rex_file::get(rex_path::media($this->name)) . PHP_EOL .
        '<!-- / ' . $this->name . ' -->';
    }

    /**
     * @api
     */
    public function getPicture(string $groupname): string
    {
        return TypeGroup::getPicture($groupname, $this);
    }

    /**
     * @api
     */
    public function setTitle(string $value = ''): void
    {
        $this->title = $value;
    }

    /**
     * @api
     */
    public static function getFrontendUrl(rex_media|self|rex_managed_media $media, string $type = '', bool $show_timestamp = true): string
    {
        if ($media instanceof rex_managed_media && $type !== '') {
            // $filename = $media->getMediaFilename();
            // Workaround wg. https://github.com/redaxo/redaxo/issues/4519#issuecomment-1183515367
            $path = explode(DIRECTORY_SEPARATOR, $media->getMediaPath() ?? '');
            $filename = array_pop($path);
            return rex_media_manager::getUrl($type, $filename, $show_timestamp, false);
        }

        if ($media instanceof rex_media || $media instanceof self) {
            $filename = $media->getFileName();
        }

        $timestamp = '';

        if ($show_timestamp) {
            $timestamp = '?timestamp=' . filemtime(rex_path::media($filename));
        }

        if ('' !== $type) {
            return rex_url::media($type . '/' . $filename) . $timestamp;
        }

        return rex_url::media($filename) . $timestamp;
    }

    /**
     * @api
     */
    public static function mediapool_updated_svg_viewbox(rex_extension_point $ep): void
    {
        $filename = (string) $ep->getParam('filename');

        $media = rex_media::get($filename);

        if (null !== $media && 'image/svg+xml' === $media->getType()) {
            $width = 0;
            $height = 0;

            $xml = simplexml_load_file(rex_path::media($filename));

            if (false !== $xml && isset($xml['viewbox'])) {
                $viewBox = preg_split('/[\s,]+/', $xml['viewBox']->__toString());

                $width = ((int) $viewBox[2] - (int) $viewBox[0]);
                $height = ((int) $viewBox[3] - (int) $viewBox[1]);

                if (0 === $height && 0 === $width && isset($xml['width']) && isset($xml['height'])) {
                    $width = $xml['width'];
                    $height = $xml['height'];
                }
            }

            $sql = rex_sql::factory();
            $sql->setWhere('filename =  :filename', [$filename => $filename]);
            $sql->setTable('rex_media');
            $sql->setValue('width', $width);
            $sql->setValue('height', $height);
            $sql->update();
        }
    }
}
