<?php
/**
 *     REX_MEDIA_PLUS[file="file.jpg" output="inline" profile="small" group="header"] // Datei file.jpg aus dem Assets-Ordner.
 */
class rex_var_media_plus extends rex_var
{
    protected function getOutput()
    {
        if ($this->hasArg('file') && $this->getArg('output')) {
            $media_plus = rex_media_plus::get($this->getArg('file'));
            $profile = $this->getArg('profile') ?? null;
            $group = $this->getArg('group') ?? null;
            if ($media_plus instanceof rex_media_plus) {
                switch ($this->getArg('output')) {
                    case 'img':
                        return self::quote($media_plus->getImg($profile));
                    case 'img-src':
                        return self::quote($media_plus->getImg($profile));
                    case 'picture':
                        return self::quote($media_plus->getPicture($group));
                    case 'img-base64':
                        return self::quote($media_plus->getImgBase64(false));
                    case 'data-base64':
                        return self::quote($media_plus->getImgBase64(true));
                    case 'svg':
                        return self::quote($media_plus->getSvg($profile));
                    case 'background':
                        return self::quote($media_plus->getBackgroundStyles($group, ''));
                    default:
                        return self::quote($media_plus->getImg($profile));
                }
            }
        }
        return self::quote('');
    }
}
