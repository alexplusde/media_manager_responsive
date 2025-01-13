# Media Manager Responsive 2 für REDAXO 5

![image](https://user-images.githubusercontent.com/3855487/162645318-e2c50a9f-6ea3-4a6c-8633-3603f9a2e4e5.png)

REDAXO-Addon mit nützlichen Methoden im Umgang mit dem Picture-Element, responsiven Bilder, SVG-Ausgabe, u.v.m.

1. Der erste Schritt: Einfach `rex_media::get()` durch `Alexplusde\MediaManagerResponsive\Media::get()` in deinem Code ersetzen und los geht's.
2. Der zweite Schritt: Eigene Responsive-Gruppen anlegen und Medientypen erstellen / zuordnen.
3. Auf Wunsch: Das Addon Cache-Warmup drüberlaufen lassen. Es werden nur tatsächlich verwendete Bildprofil-Mediendatei-Kombinationen generiert.

## Features

* Vereinfachte Ausgabe von Picture-Elementen und Bildern mit srcset-Attributen
* Direkte Ausgabe von SVGs ohne Image-Tag, z.B. für Logos und Icons
* Google PageSpeed-, Ladezeit und Benutzererlebnis-Optimierungen
  * Automatisches hinzufügen von Timestamps an die URL eines Mediums, um exzessives Caching aktivieren zu können
  * Vollständige Ausgabe der Meta-Informationen am Image- oder Picture-Element: Korrekte Höhe und Breite von Bildern werden als `width="XXX" height="XXX"` ausgegeben.
  * Cachebuster-URLs für Medien
* Optimiert für Google Structured Data - Meta-Informationen für die Google-Suchmaschine.
* Kompatibel zu `FriendsOfRedaxo\minify_images` und `FriendsOfRedaxo\focuspoint`
* Optimiert für <https://github.com/FriendsOfREDAXO/cache_warmup>
* * NEU mit V2: Injector, der Media Manager Types auch in WYSIWYG-Editoren zur Laufzeit injiziert

## `Alexplusde\MediaManagerResponsive\Media` verwenden

Die Klasse `Media` ist eine Child-Klasse und erbt alle Methoden von `rex_media` bis auf nachfolgende Ergänzungen und Ersetzungen.

### `getFrontendUrl()`

Liefert eine URL zum Frontend einschließlich Timestamp (Cachebuster), z.B. `/media/image.ext?timestamp=XXXXXX`

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

### `setTitle()` 

Standardmäßig nutzt die Klasse das Titel-Feld des Medienpools für das Alt-Attribut des Bildes. Überschreibe das alt-Attribut auf Wunsch mit einem eigenen Text.

### Beispiele mit REX_VARS

```php
/* ERWEITERT REX_MEDIA - Ein Bild wird optimiert ausgegeben */
REX_MEDIA_PLUS[file="file.png" output="img" profile="small"]
REX_MEDIA_PLUS[file="file.svg" output="svg"]
REX_MEDIA_PLUS[file="file.jpg" output="img-base64"]
REX_MEDIA_PLUS[file="file.jpg" output="img-src" profile="small"]

/* NUTZT MEDIA MANAGER RESPONSIVE-GRUPPEN */
REX_MEDIA_PLUS[file="file.jpg" output="picture" group="header"]
REX_MEDIA_PLUS[file="file.jpg" output="background" group="header"]
```

### Beispiele mit PHP

```php
use Alexplusde\MediaManagerResponsive\Media;

echo Media::get("beispielbild.jpg")->getImg($type);
echo Media::get("beispielbild.jpg")->setClass("img-fluid")->setTitle('Das ist mein Alt-Attribut')->getImg($type);
echo Media::get("beispielbild.jpg")->getBase64();
echo Media::get("beispielbild.jpg")->getPicture($group);
echo Media::get("beispielbild.jpg")->getBackgroundStyles($group);
```

## Einstellungen

Neu in Version 2: Media Manager Responsive kann nun auch im WYSIWYG-Editor verwendet werden. Dazu muss ein Standard-Media-Manager-Typ in den Einstellungen von Media Manager Responsive ausgewählt werden.

Anschließend wird zur Laufzeit der Media Manager Typ in den WYSIWYG-Editor injiziert, inklusive korrekter Höhen- und Breitenangaben des gecachten Bildes.

```html
<!-- Vorher -->
<img src="/media/mein-bild.jpg" alt="Bildbeschreibung" />
<!-- Nachher -->
<img src="/media/gewaehltes-profil/mein-bild.jpg" alt="Bildbeschreibung" width="1600" height="900" load="auto" />
```

## Medien Manager Responsive verwenden

> **Tipp**: Kombiniere dieses Addon mit [Media Negotiator von AndiLeni](https://github.com/AndiLeni/media_negotiator), dieser erlaubt es, die ausgespielten Dateiformate je nach Browser und Gerät zu optimieren. Dann müssen keine separaten WEBP-Profile oder AVIF-Profile angelegt werden.

### Beispiel-Medientypen-Liste

![image](https://user-images.githubusercontent.com/3855487/162642967-8dee2322-2702-4486-85fb-e988cbe8ef37.png)

### Beispiel Responsive-Profil

> Hinweis: Der Screenshot zeigt die Reihenfolge gerade verkehrt herum zu dem, wie es sein sollte. Eine automatische Sortierung ist nicht möglich, da es verschiedene Möglichkeiten und Stile gibt, die Profile zu definieren. Prüfe die richtige Lade-Reihenfolge von Dateiformat (type) und Medium (media) in Abhängigkeit der gewählten Einstellungen in der Broser-Entwicklerkonsole.

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

### Wie verbessert dieses Addon meinen PageSpeed?

Richtig eingesetzt durch die Kombination von 3 Features, die sowohl von Google PageSpeed berücksichtigt werden, als auch das Nutzererlebenis für die Besucher verbessern:

1. Durch die parallele Nutzung verschiedener Dateiformate (z.B. WEBP+JPG) und durch Media-Querys, bspw. beim `<picture>`-Element oder mehreren `<img srcset>`-Profilen. Dadurch wählt der Browser des jeweiligen Geräts die passende Auflösung und Bilddateien können bereits im Media Manager verkleinert werden. Statt Bilder mit hunderten an Kilobytes müssen in den jeweiligen Szenarien nur wenige Kilobyte pro Bild übertragen werden.
2. Durch Zeitstempel an den jeweiligen Bild-URLs, z.B. `/mediaprofil/beispiel.jpg?timestamp=1234567890`. Richtig eingesetzt kann in der `.htaccess` oder an anderer Stelle des Servers das Bild ewig gecached werden - im REDAXO-Kontext auch als "Cache-Buster" bekannt. Nur wenn das Bild sich tatsächlich ändert, ändert sich die URL.
3. Durch das Auslesen der tatsächlichen Höhen- und Breitenangaben der Bilder - auch von gecachten Media Manager Bildern - ist der Browser in der Lage, Platz für die Abmaße der Bilder zu schaffen, bevor diese geladen sind, und somit Verschiebungen beim Laden und Rendern der Website elimiert werden. Keine nervigen "Layout-Sprünge" mehr.

Google PageSpeed-Scores von deutlich über 90 Punkten sowohl Mobil, als auch am Desktop, sind damit problemlos möglich.

### Was ist der Unterschied zum Addon `media_manager_plus`?

Media Manager Responsive kommt mit zahlreichen Erweiterungen, die es so in `media_manager_plus` nicht gibt. Darüber hinaus verzichtet Media Manager Responsive auf externe Bibliotheken wie `lazysizes`, sondern setzt voll und ganz auf moderne Browser-Features und bleibt bei korrekter Konfiguration (bspw. Fallback-Formaten) dennoch kompatibel.

### Warum dauert der erste Seitenaufruf nach Verwendung sehr lange?

Puh, statt dass jedes Bild nur ein Media-Manager-Profil durchläuft, sind es je nach gewählten Einstellungen beliebig viele Bilder. Beim Autor dieses Addons sind das schnell mal 18 Varianten pro Bild ((S, M, L) *(JPG, WEBP)* (1x, 2x, 3x) = 18 Kombinationen je Bild) Diese werden aus Optimierungsgründen auch alle in einem Rutsch erstellt.

Bei einer Galerie mit 12 Bildern bedeutet dies statt 12 generierter Bilder nun bspw. 216 Bilder, deren Cache-Versionen erstellt werden.

Ein entsprechend performantes Webhosting-Paket und ausreichend Speicherplatz werden daher empfohlen.

Abhilfe schafft das Addon <https://github.com/FriendsOfREDAXO/cache_warmup> - da die Bilder zur Laufzeit generiert werden, können bei umfangreichen Seiten alle benötigten Bildkombinationen in einem Rutsch generiert werden.

## Lizenz

[MIT Lizenz](https://github.com/alexplusde/media_manager_responsive/blob/master/LICENSE.md)

## Autor

**Alexander Walther**
<https://www.alexplus.de>

**Projekt-Lead**
[Alexander Walther](https://www.alexplus.de)
