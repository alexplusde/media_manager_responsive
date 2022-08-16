<?php
/**
 *     REX_MEDIA_PLUS[file="file.jpg" output="inline" profile="small" group="header"] // Datei file.jpg aus dem Assets-Ordner
 */
class rex_var_media_plus extends rex_var
{
    protected function getOutput()
    {
        if ($this->hasArg('file') && $this->getArg('output')) {
            $media_plus = rex_media_plus::get($this->getArg($file));
            $profile = $this->getArg('profile') ?? null;
            $group = $this->getArg('group') ?? null;
            if ($media_plus instanceof rex_media_plus) {
                switch ($this->getArg('output')) {
                    case 'img':
                        return self::quote($media->getImg($profile));
                        break;
                    case 'img-src':
                        return self::quote($media->getImg($profile));
                        break;
                    case 'picture':
                        return self::quote($media->getPicture($group));
                        break;
                    case 'img-base64':
                        return self::quote($media->getImgBase64(false));
                        break;
                    case 'data-base64':
                        return self::quote($media->getImgBase64(true));
                        break;
                    case 'svg':
                        return self::quote($media->getSvg($profile));
                        break;
                    case 'background':
                        return self::quote($media->getBackgroundStyles($group, $selector));
                        break;
                    default:
                        return self::quote($media->getImg($profile));
                }
            }
        }
        return self::quote();
    }
}
