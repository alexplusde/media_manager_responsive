<?php

namespace Alexplusde\MediaManagerResponsive;

use rex_yform_manager_dataset;
use rex_yform_manager_collection;

class Type extends rex_yform_manager_dataset
{
    /**
     * @api
     * @return null|rex_yform_manager_collection<TypeGroup>
     */
    public function getGroup(): ?rex_yform_manager_collection
    {
        return $this->getRelatedCollection('group');
    }

    /**
     * @api
     * @return string 
     */
    public function getType(): string
    {
        return $this->getValue('type');
    }

    /**
     * @api
     * @return string 
     */
    public function getRatio(): string
    {
        return $this->getValue('ratio');
    }

    /**
     * @api
     * @return string 
     */
    public function getMinWidth(): string
    {
        return $this->getValue('min_width');
    }

    /**
     * @api
     * @return string 
     */
    public function getMaxWidth(): string
    {
        return $this->getValue('max_width');
    }
}
