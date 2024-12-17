<?php

class MediaManagerTypeInjector
{
    private static function addTypeToPath(string $type, string $profilePath): string
    {
        return '/media/' . trim($profilePath, '/') . '/' . ltrim(str_replace('/media/', '', $type), '/');
    }

    public static function injectType(string $html, string $type = 'default'): string
    {
       
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $images = $xpath->query('//img[not(@srcset) and not(@width) and not(@height) and not(ancestor::picture) and not(ancestor::figure)]');

        if ($images === false) {
            throw new RuntimeException('XPath-Abfrage fehlgeschlagen');
        }

        foreach ($images as $img) {
            $src = $img->attributes->getNamedItem('src')->nodeValue;
            if (empty($src)) {
                continue;
            }
            
            $urlParts = parse_url($src);
            if ($urlParts === false) {
                continue;
            }
            
            $pathToCheck = $urlParts['path'] ?? $src;
            
            if (strpos($pathToCheck, '/media/') === 0 && !preg_match('#^/media/[^/]+/#', $pathToCheck)) {
                // Sammle alle Attribute
                $imgAttributes = [];
                $media = null;
                foreach ($img->attributes as $attribute) {
                    $imgAttributes[$attribute->name] = $attribute->value;
                    if ($attribute->name == 'src') {
                        // Dateiname ohne Pfad
                        $filename = basename($attribute->value);
                        $media = rex_media_plus::get($filename);
                    }
                }

                if ($media === null) {
                    continue;
                }


                $newImageString = $media->getImgAsAttributesArray($type);
                $imgAttributes = array_merge($imgAttributes, $newImageString);

                if (isset($urlParts['scheme'], $urlParts['host'])) {
                    // Absolute URL
                    $newPath = self::addTypeToPath($pathToCheck, $type);
                    $newSrc = $urlParts['scheme'] . '://' . $urlParts['host'] . $newPath;
                    if (isset($urlParts['query'])) {
                        $newSrc .= '?' . $urlParts['query'];
                    }
                    if (isset($urlParts['fragment'])) {
                        $newSrc .= '#' . $urlParts['fragment'];
                    }
                } else {
                    // Relativer Pfad
                    $newSrc = self::addTypeToPath($pathToCheck, $type);
                }
                
                $imgAttributes['src'] = $newSrc;

                foreach ($imgAttributes as $name => $value) {
                    $attributes = $img->attributes;
                    /** @var DOMNamedNodeMap $attributes */
                    if ($attributes->getNamedItem($name) === null) {
                        $newAttribute = $dom->createAttribute($name);
                        $newAttribute->value = $value;
                        $img->appendChild($newAttribute);
                    } else {
                        $img->attributes->getNamedItem($name)->nodeValue = $value;
                    }
                }

                $img->attributes->getNamedItem('src')->nodeValue = $newSrc;
            }
            
        }

        return $dom->saveHTML();
    }
}

// Beispiel Verwendung:
// $modifiedHtml = MediaManagerProfileInjector::injectProfile($html, 'profile');
