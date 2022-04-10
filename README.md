# Media Manager Responsive für REDAXO 5

REDAXO-Addon mit nützlichen Methoden im Umgang mit dem Picture-Element, responsiven Bilder, SVG-Ausgabe, u.v.m.

Der erste Schritt: Einfach `rex_media::get()` durch `rex_media_plus::get()` in deinem Code ersetzen und los geht's.

Der zweite Schritt: Eigene Responsive-Gruppen anlegen und Medientypen erstellen / zuordnen.

> DER NACHFOLGENDE TEIL DER README WIRD DERZEIT AN DEN NEUEN CODE ANGEGLICHEN UND IST ERST ZU RELEASE GÜLTIG

## Features

* Vereinfachte Ausgabe von Picture-Elementen und Bildern mit srcset-Attributen
* Automatisches hinzufügen von Timestamps an die URL eines Mediums, um exzessives Caching aktivieren zu können
* Direkte Ausgabe von SVGs ohne Image-Tag, z.B. für Logos und Icons 
* Optimiert für Google PageSpeed: Vollständige Ausgabe der Meta-Informationen am Image- oder Picture-Element: Korrekte Höhe und Breite von Bildern werden als `width="XXX" height="XXX"` ausgegeben.
* Optimiert für Google Structured Data - Meta-Informationen für die Google-Suchmaschine.

## `rex_media_plus` verwenden

Die Klasse `rex_media_plus` ist eine Child-Klasse und erbt alle Methoden von `rex_media` bis auf nachfolgende Ergänzungen und Ersetzungen.

### `getFrontendUrl()`

Liefert eine URL zum Frontend einschließlich Timestamp, z.B. `/media/image.ext?timestamp=XXXXXX`

### `getSvg()`

Liefert den kompletten Inhalt eines SVGs

### `getBase64()`

Liefert den kompletten Inhalt eines Mediums Base64-kodiert

### `getPicture($group_name)`

Liefert ein vollständiges Picture-Element anhand eines Präfix inkl. `<sources>`-Elementen. Dazu muss es passende Media Manager Profile mit einer bestimmten Syntax geben, die unter "Media Manager Profile" in dieser Hilfe-Datei erklärt wird.

### `getSrcset($group_name)`

Liefert ein vollständiges Image-Element anhand eines Präfix inkl. `srcset=""`-Attribut. Dazu muss es passende Media Manager Profile mit einer bestimmten Syntax geben, die unter "Media Manager Profile" in dieser Hilfe-Datei erklärt wird.

### `getBackgroundStyles($group_name)`

Liefert ein vollständiges Image-Element anhand eines Präfix inkl. `srcset=""`-Attribut. Dazu muss es passende Media Manager Profile mit einer bestimmten Syntax geben, die unter "Media Manager Profile" in dieser Hilfe-Datei erklärt wird.

## Medien Manager Responsive verwenden

Wird erläutert.

## Screenshots

### Beispiel-Medientypen-Liste

![image](https://user-images.githubusercontent.com/3855487/162642967-8dee2322-2702-4486-85fb-e988cbe8ef37.png)

### Beispiel Responsive-Profil

![image](https://user-images.githubusercontent.com/3855487/162643004-cc5614c2-e043-4a9b-a118-231853608b53.png)

### Beispiel-Code `<picture>`-Element

```html
<picture>
    <source media="(min-width: 1px)" sizes="" type="image/jpeg" data-width="480" data-height="321"
        srcset="/media/480w_1x/beispielbild.jpg?timestamp=1649629920">
    <source media="(min-width: 1px)" sizes="" type="image/webp" data-width="1024" data-height="683"
        srcset="/media/1920w_1x_webp/beispielbild.jpg?timestamp=1649629920">
    <source media="(min-width: 1px)" sizes="" type="image/jpeg" data-width="960" data-height="641"
        srcset="/media/480w_2x/beispielbild.jpg?timestamp=1649629921">
    <source media="(min-width: 1px)" sizes="" type="image/webp" data-width="960" data-height="641"
        srcset="/media/480w_2x_webp/beispielbild.jpg?timestamp=1649629921">
    <source media="(min-width: 480px)" sizes="" type="image/jpeg" data-width="720" data-height="481"
        srcset="/media/720w_1x/beispielbild.jpg?timestamp=1649629921">
    <source media="(min-width: 480px)" sizes="" type="image/webp" data-width="720" data-height="481"
        srcset="/media/720w_1x_webp/beispielbild.jpg?timestamp=1649629921">
    <source media="(min-width: 480px)" sizes="" type="image/jpeg" data-width="1024" data-height="683"
        srcset="/media/720w_2x/beispielbild.jpg?timestamp=1649629921">
    <source media="(min-width: 480px)" sizes="" type="image/webp" data-width="1024" data-height="683"
        srcset="/media/720w_2x_webp/beispielbild.jpg?timestamp=1649629921">
    <source media="(min-width: 720px)" sizes="" type="image/jpeg" data-width="1024" data-height="683"
        srcset="/media/1920w_1x/beispielbild.jpg?timestamp=1649629921">
    <source media="(min-width: 720px)" sizes="" type="image/webp" data-width="1024" data-height="683"
        srcset="/media/1920w_1x_webp/beispielbild.jpg?timestamp=1649629920">
    <source media="(min-width: 720px)" sizes="" type="image/jpeg" data-width="1024" data-height="683"
        srcset="/media/1920w_2x/beispielbild.jpg?timestamp=1649629921">
    <source media="(min-width: 720px)" sizes="" type="image/jpeg" data-width="1024" data-height="683"
        srcset="/media/1920w_2x_webp/beispielbild.jpg?timestamp=1649629921">
    <source media="(min-width: 1920px)" sizes="" type="image/jpeg" data-width="1024" data-height="683"
        srcset="/media/1920w_2x/beispielbild.jpg?timestamp=1649629921">
    <source media="(min-width: 1920px)" sizes="" type="image/jpeg" data-width="1024" data-height="683"
        srcset="/media/1920w_2x_webp/beispielbild.jpg?timestamp=1649629921"><img style="width: 100%; height: auto;"
        type="image/jpeg" src="/media/1920w_1x/beispielbild.jpg?timestamp=1649629921" width="1024" height="683" alt="">
</picture>
```

### Beispiel-Code `<style>` für responsive Hintergrundbilder

```html
<!-- Media Plus Background-Styles for "beispiel.jpg" -->
<style>
    @media(min-width: 1px) { #title-bg-image{ background-image: url(/media/bs5.title.3x1_s/beispiel.jpg?timestamp=1649631662);} }
    @media(min-width: 1px) { #title-bg-image{ background-image: url(/media/bs5.title.3x1_s_webp/beispiel.jpg?timestamp=1649631680);} }
    @media(min-width: 480px) { #title-bg-image{ background-image: url(/media/bs5.title.3x1_m/beispiel.jpg?timestamp=1649631681);} }
    @media(min-width: 480px) { #title-bg-image{ background-image: url(/media/bs5.title.3x1_m_webp/beispiel.jpg?timestamp=1649631682);} }
    @media(min-width: 1024px) { #title-bg-image{ background-image: url(/media/bs5.title.3x1_l/beispiel.jpg?timestamp=1649631683);} }
    @media(min-width: 1024px) { #title-bg-image{ background-image: url(/media/bs5.title.3x1_l_webp/beispiel.jpg?timestamp=1649631684);} }
</style>    
<div id="title-bg-image" style="background-size: cover; background-position: center">
</div>
```

## FAQ

### Warum dauert der erste Seitenaufruf nach Verwendung sehr lange?

Puh, statt dass jedes Bild nur ein Media-Manager-Profil durchläuft, sind es je nach gewählten Einstellungen beliebig viele Bilder. Beim Autor dieses Addons sind das schnell mal 18 Varianten pro Bild ((S, M, L) * (JPG, WEBP) * (1x, 2x, 3x) = 18 Kombinationen je Bild) Diese werden aus Optimierungsgründen auch alle in einem Rutsch erstellt.

Bei einer Galerie mit 12 Bildern bedeutet dies statt 12 generierter Bilder nun bspw. 216 Bilder, deren Cache-Versionen erstellt werden.

Ein entsprechend performantes Webhosting-Paket und ausreichend Speicherplatz werden daher empfohlen.

## Lizenz

[MIT Lizenz](https://github.com/alexplusde/media_manager_responsive/blob/master/LICENSE.md) 

## Autor

**Alexander Walther**
https://www.alexplus.de

**Projekt-Lead** 
[Alexander Walther](https://www.alexplus.de)
