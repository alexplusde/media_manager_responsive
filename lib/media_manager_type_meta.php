<?php
class media_manager_type_meta extends \rex_yform_manager_dataset
{
    public function getGroup()
    {
        return $this->getCollection('group');
    }
    public function getType()
    {
        return $this->getValue('type');
    }
    public function getRatio()
    {
        return $this->getValue('ratio');
    }
    public function getMinWidth()
    {
        return $this->getValue('min_width');
    }
    public function getMaxWidth()
    {
        return $this->getValue('max_width');
    }
}
