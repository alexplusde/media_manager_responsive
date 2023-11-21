<?php
class media_manager_type_meta extends \rex_yform_manager_dataset
{
    public function getGroup(): ?rex_yform_manager_collection
    {
        return $this->getRelatedCollection('group');
    }
    public function getType() :string
    {
        return $this->getValue('type');
    }
    public function getRatio() :string
    {
        return $this->getValue('ratio');
    }
    public function getMinWidth() :int
    {
        return $this->getValue('min_width');
    }
    public function getMaxWidth() :int
    {
        return $this->getValue('max_width');
    }
}
