<?php

class rex_media_plus extends rex_media
{
    public string $loading = 'auto';
    public string $class = '';
    public array $plus_attributes = [];

    public function __construct()
    {
        $this->attributes = [];
    }

    public function getLoading(): string
    {
        return $this->loading;
    }

    public function setLoading(string $loading): self
    {
        $this->loading = $loading;

        return $this;
    }

    public function setClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function setAttributes(array $attributes): self
    {
        $this->plus_attributes = $attributes;

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->plus_attributes ?? [];
    }

    /**
     * @api
     * @return string 
     * @throws InvalidArgumentException 
     */
    public function getStructuredData(): string
    {
        $fragment = new rex_fragment();
        $fragment->setVar('title', $this->getTitle() ?? '');
        $fragment->setVar('description', $this->getValue('med_description'));
        $fragment->setVar('author', $this->getValue('med_copyright') ?? '');
        $fragment->setVar('location', $this->getValue('med_location') ?? '');
        $fragment->setVar('name', $this->getValue('name'));
        $fragment->setVar('datePublished', date('Y-m-d', $this->getUpdatedate()));

        return html_entity_decode($fragment->parse('media_manager_responsive/structured_data.php'));
    }

    /**
     * @api
     * @param string $group_name 
     * @param string $selector 
     * @return string 
     * @throws InvalidArgumentException 
     * @throws RuntimeException 
     * @throws rex_sql_exception 
     * @throws rex_exception 
     */
    public function getBackgroundStyles(string $group_name, string $selector)
    {
        return media_manager_type_group::getBackgroundStyles($this->getFilename(), $group_name, $selector);
    }

    /**
     * @api
     * @param null|string $profile 
     * @return string 
     */
    public function getImg(?string $profile = null): string
    {
        if ($profile) {
            return '<img ' . implode(' ', $this->getAttributes()) . ' class="' . $this->getClass() . '" src="' . self::getFrontendUrl($this, $profile) . '" alt="' . $this->getTitle() . '" width="' . $this->getWidth() . '" height="' . $this->getHeight() . '" />';
        }
        return '<img ' . implode(' ', $this->getAttributes()) . ' class="' . $this->getClass() . '" src="' . self::getFrontendUrl($this) . '" alt="' . $this->getTitle() . '" width="' . $this->getWidth() . '" height="' . $this->getHeight() . '" />';
    }

    /**
     * @api
     * @param bool $only_data 
     * @return string 
     * @throws InvalidArgumentException 
     */
    public function getImgBase64(bool $only_data = false): string
    {
        $data = 'data:image/' . $this->getType() . ';base64,' . base64_encode(rex_file::get(rex_path::media($this->name)));
        if ($only_data) {
            return $data;
        }
        return '<img ' . implode(' ', $this->getAttributes()) . ' class="' . $this->getClass() . '" src="' . $data . '" alt="' . $this->getTitle() . '" width="' . $this->getWidth() . '" height="' . $this->getHeight() . '" />';
    }

    /**
     * @api
     * @return string 
     * @throws InvalidArgumentException 
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
     * @param string $groupname 
     * @return string 
     * @throws RuntimeException 
     * @throws rex_sql_exception 
     * @throws rex_exception 
     */
    public function getPicture(string $groupname): string
    {
        return media_manager_type_group::getPicture($groupname, $this);
    }

    /**
     * @api
     * @param string $value 
     * @return void 
     */
    public function setTitle(string $value = ''): void
    {
        $this->title = $value;
    }

    /**
     * @api
     * @param rex_media|rex_media_plus|rex_managed_media $media 
     * @param null|string $profile 
     * @param bool $show_timestamp 
     * @return string 
     */
    public static function getFrontendUrl(rex_media|self|rex_managed_media $media, ?string $profile = null, bool $show_timestamp = true): string
    {
        if ($media instanceof rex_media || $media instanceof self) {
            $filename = $media->getFileName();
        } elseif ($media instanceof rex_managed_media) {
            // $filename = $media->getMediaFilename();
            // Workaround wg. https://github.com/redaxo/redaxo/issues/4519#issuecomment-1183515367
            $path = explode(DIRECTORY_SEPARATOR, $media->getMediaPath());
            $filename = array_pop($path);
        }

        $timestamp = '';

        if ($show_timestamp) {
            if ($media instanceof rex_managed_media) {
                $timestamp = '?timestamp=' . filectime($media->getSourcePath());
            } elseif ($media instanceof rex_media || $media instanceof self) {
                $timestamp = '?timestamp=' . filectime(rex_path::media($filename));
            }
        }

        if ($profile !== null) {
            return rex_url::media($profile . '/' . $filename) . $timestamp;
        }

        return rex_url::media($filename) . $timestamp;
    }

    /**
     * @api
     * @param rex_extension_point $ep 
     * @return void 
     * @throws rex_sql_exception 
     */
    public static function mediapool_updated_svg_viewbox(rex_extension_point $ep) :void
    {
        $filename = (string) $ep->getParam('filename');

        $media = rex_media::get($filename);

        if ($media !== null && 'image/svg+xml' === $media->getType()) {
            $xml = simplexml_load_file(rex_path::media($filename));

            $viewBox = $xml['viewBox'] ? $xml['viewBox']->__toString() : 0;
            $viewBox = preg_split('/[\s,]+/', $viewBox);

            $width = 0;
            $height = 0;

            $width = (int) ((int) $viewBox[2] - (int) $viewBox[0]);
            $height = (int) ((int) $viewBox[3] - (int) $viewBox[1]);


            if (!$height && !$width && isset($xml['width']) && isset($xml['height'])) {
                $width = $xml['width'] ? $xml['width']->__toString() : 0;
                $height = $xml['height'] ? $xml['height']->__toString() : 0;
            }

            $sql = rex_sql::factory();
            $sql->setWhere('filename', $filename);
            $sql->setTable('rex_media');
            $sql->setValue('width', $width);
            $sql->setValue('height', $height);
            $sql->update();
        }
    }
}
