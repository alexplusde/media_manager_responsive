<?php

namespace Alexplusde\MediaManagerResponsive;

/**
 * @deprecated Use Alexplusde\MediaManagerResponsive\Media instead.
 */
class rex_media_plus extends Media
{
    public function __construct()
    {
        trigger_error('Class rex_media_plus is deprecated. Use Alexplusde\MediaManagerResponsive\Media instead.', E_USER_DEPRECATED);
        parent::__construct();
    }
}
