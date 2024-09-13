<?php

namespace Alexplusde\MediaManagerResponsive;

use rex_yform_manager_collection;
use rex_yform_manager_dataset;

class Type extends rex_yform_manager_dataset
{
    /**
     * @api
     * @return rex_yform_manager_collection<TypeGroup>|null
     */
    public function getGroup(): ?rex_yform_manager_collection
    {
        return $this->getRelatedCollection('group');
    }

    /**
     * @api
     */
    public function getType(): string
    {
        return $this->getValue('type');
    }

    /**
     * @api
     */
    public function getRatio(): string
    {
        return $this->getValue('ratio');
    }

    /**
     * @api
     */
    public function getMinWidth(): string
    {
        return $this->getValue('min_width');
    }

    /**
     * @api
     */
    public function getMaxWidth(): string
    {
        return $this->getValue('max_width');
    }
}
