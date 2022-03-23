# Media Plus für REDAXO 5

REDAXO-Addon mit nützlichen Methoden im Umgang mit dem Picture-Element, Responsive Bilder, SVG-Ausgabe, u.v.m.

Einfach `rex_media::get()` durch `media_plus::get()` in deinem Code ersetzen und los geht's.

## Features

* Vereinfachte Ausgabe von Picture-Elementen und Bildern mit srcset-Attributen
* Automatisches hinzufügen von Timestamps an die URL eines Mediums, um exzessives Caching aktivieren zu können
* Direkte Ausgabe von SVGs ohne Image-Tag, z.B. für Logos und Icons 
* Optimiert für Google Page Speed: Vollständige Ausgabe der Meta-Informationen am Image- oder Picture-Element: Korrekte Höhe und Breite von Bildern werden als `width="XXX" height="XXX"` ausgegeben.
* Optimiert für Google Structured Data - Meta-Informationen für die Google-Suchmaschine.

## Media Plus Verwenden

Die Klasse `media_plus` ist eine Child-Klasse und erbt alle Methoden von `rex_media` bis auf nachfolgende Ergänzungen und Ersetzungen.

### `getUrl()`

Liefert eine URL zum Frontend einschließlich Timestamp, z.B. `/media/image.ext?timestamp=XXXXXX`


### `getSvg()`

Liefert den kompletten Inhalt eines SVGs

### `getBase64()`

Liefert den kompletten Inhalt eines Mediums Base64-kodiert

### `getPicture($profile_prefix)`

Liefert ein vollständiges Picture-Element anhand eines Präfix inkl. `<sources>`-Elementen. Dazu muss es passende Media Manager Profile mit einer bestimmten Syntax geben, die unter "Media Manager Profile" in dieser Hilfe-Datei erklärt wird.

### `getSrcset($profile_prefix)`

Liefert ein vollständiges Image-Element anhand eines Präfix inkl. `srcset=""`-Attribut. Dazu muss es passende Media Manager Profile mit einer bestimmten Syntax geben, die unter "Media Manager Profile" in dieser Hilfe-Datei erklärt wird.

## Managed Media Plus verwenden

Wird erläutert.

## Lizenz

[MIT Lizenz](https://github.com/alexplusde/be_style_fluent/blob/master/LICENSE.md) 

## Autor

**Alexander Walther**
https://www.alexplus.de

**Projekt-Lead** 
[Alexander Walther](https://www.alexplus.de)
