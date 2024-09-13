<?php

class media_manager_type_meta extends rex_yform_manager_dataset
{
    /**
     * @api
     * @return null|rex_yform_manager_collection 
     * @throws rex_exception 
     * @throws InvalidArgumentException 
     * @throws RuntimeException 
     * @throws rex_sql_exception 
     */
    public function getGroup(): ?rex_yform_manager_collection
    {
        return $this->getRelatedCollection('group');
    }

    /**
     * @api
     * @return string 
     * @throws rex_sql_exception 
     */
    public function getType(): string
    {
        return $this->getValue('type');
    }

    /**
     * @api
     * @return string 
     * @throws rex_sql_exception 
     */
    public function getRatio(): string
    {
        return $this->getValue('ratio');
    }

    /**
     * @api
     * @return string 
     * @throws rex_sql_exception 
     */
    public function getMinWidth(): string
    {
        return $this->getValue('min_width');
    }

    /**
     * @api
     * @return string 
     * @throws rex_sql_exception 
     */
    public function getMaxWidth(): string
    {
        return $this->getValue('max_width');
    }
}
